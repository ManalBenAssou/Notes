<?php
require_once 'model/User.php';
require_once 'model/TextNote.php';
require_once 'model/CheckListNote.php';
require_once 'model/NoteShare.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerNote extends Controller {
    var mixed $data ;
    
    public function index() : void{
    
        $this->displayIconBar();
    }
    public function displayIconBar() {
        //$noteId = Note::getId();

        $note = Note::getNoteById(27);
        (new View("open_note"))->show(["note" => $note]);

    }

    public function labels(): void {
        $user = $this->get_user_or_redirect();
        $noteId = $this->getIdUrl();
        $note = Note::getNoteById($noteId);
        $dataUrl = isset($_GET['param2']) ? $_GET['param2'] : '';
        $noteLabels = $note->getLabels();
        $labelsList = Note::getAllUserLabels($user->getId());
        if (!empty($noteLabels)) {
            // Enlever les labels de $noteLabels de la liste complète des labels $labelsList
            $labelsList = array_diff($labelsList, $noteLabels);
        }

        (new View("labels"))->show(["noteLabels"=>$noteLabels, "noteId"=>$noteId, "labelsList"=>$labelsList, "dataUrl"=>$dataUrl]);
    } 
    public function deleteLabel() : void {
        $noteId = isset($_POST['noteId']) ? (int)$_POST['noteId'] : null;
        $label = isset($_POST['label']) ? $_POST['label'] : null;
        $dataUrl = isset($_GET['param1']) ? $_GET['param1'] : '';
        if ($noteId != null) {
            $note = Note::getNoteById($noteId);
            $note->deleteLabel($label);
            $this->redirect("note","labels",$noteId,$dataUrl);
        }

    }
    public function addLabel() : void {
        $user = $this->get_user_or_redirect();
        $noteId = isset($_POST['noteId']) ? (int)$_POST['noteId'] : null;
        $label = isset($_POST['label']) ? $_POST['label'] : null;
        $dataUrl = isset($_GET['param1']) ? $_GET['param1'] : '';
        $errors = [];
        if ($noteId != null) {
            $note = Note::getNoteById($noteId);

            $errors = Note::validateLabel($label);
            if (Note::isLabelExist($noteId, $label)) {
                $errors[] = "A note cannot contain the same label twice";
            }
            if (empty($errors)) {
                $note->addLabel($label);
                $this->redirect("note","labels",$noteId,$dataUrl);
            }
            else {
                $noteLabels = $note->getLabels();
                $labelsList = Note::getAllUserLabels($user->getId());
                // Enlever les labels de $noteLabels de la liste complète des labels $labelsList
                $labelsList = array_diff($labelsList, $noteLabels);
                
                (new View("labels"))->show(["noteLabels"=>$noteLabels, "noteId"=>$noteId, "errors" =>$errors, "labelsList"=>$labelsList,"dataUrl"=>$dataUrl]);
            }
        }
    }
    public function addLabelJs() : void {
        $user = $this->get_user_or_redirect();
        $noteId = isset($_POST['noteId']) ? (int)$_POST['noteId'] : null;
        $label = isset($_POST['label']) ? $_POST['label'] : null;
        $dataUrl = isset($_GET['param1']) ? $_GET['param1'] : '';
        $errors = [];
        if ($noteId != null) {
            $note = Note::getNoteById($noteId);

            $errors = Note::validateLabel($label);
            if (Note::isLabelExist($noteId, $label)) {
                $errors[] = "A note cannot contain the same label twice";
            }
            if (empty($errors)) {
                $note->addLabel($label);
                $response = [
                    'label' => $label
                ];
                echo json_encode($response);
                return;   
            }
            
        }
    }
    

    public function labelExistsService() : void {
        $res = "false";
        $label = $_POST['label'];
        $noteId = $_POST['noteId'];
        $note = Note::getNoteById($noteId);
        $noteLabels = $note->getLabels();

        if(in_array($label, $noteLabels))
            $res =  "true"; 

        echo $res;
    }

    public function search() : void {
        $user = $this->get_user_or_redirect();
        $labels = Note::getAllUserLabels($user->getId());
        if (isset($_POST["checkedLabels"])) {
            $checkedLabels = isset($_POST["checkedLabels"]) ? $_POST["checkedLabels"] : "";
            $encodedUrl = Tools::url_safe_encode($checkedLabels);
            $this->redirect("note", "searchNotes", $encodedUrl);
        }
        else {
            (new View("search_notes"))->show(["user" => $user, "labels"=>$labels]);
        }
    }

    public function searchNotes() : void {
        $user = $this->get_user_or_redirect();
        $myNotes = [];
        $allSharedNotes = [];
        $labels = Note::getAllUserLabels($user->getId());

        //if (isset($_POST["checkedLabels"])) {
            $this->data = $_POST["checkedLabels"];

            $checkedLabels = isset($_GET['param1']) ? Tools::url_safe_decode($_GET['param1']) : $_POST["checkedLabels"];
            $encodedUrl = Tools::url_safe_encode($checkedLabels);

            foreach ($checkedLabels as $label) {
                $tab = $user->getMyNotesByLabel($label);
                
                foreach($tab as $noteTab){
                    //éviter d'ajouter les doublons
                    if(!in_array($noteTab, $myNotes))
                        $myNotes[] = $noteTab;
                }
                
                // Concaténez les résultats de chaque étiquette à $notes au lieu de les écraser à chaque itération
                //$myNotes = array_merge($myNotes, $user->getMyNotesByLabel($label));
                $allSharedNotes = array_merge($allSharedNotes, $user->getSharedNotesByLabel($label));

            }
            
            foreach ($allSharedNotes as $note) {
                foreach ($checkedLabels as $label) {
                    // Vérifier si la note a le label
                    if (!Note::isLabelExist($note->getId(), $label)) {
                        // Retirer la note du tableau $myNotes si elle n'a pas le label
                        $allSharedNotes = array_filter($allSharedNotes, function($n) use ($note) {
                            return $n !== $note;
                        });
                        // Sortir de la boucle foreach pour éviter de retirer la même note plusieurs fois
                        break;
                    }
                }
            }

            foreach ($myNotes as $note) {
                foreach ($checkedLabels as $label) {
                    // Vérifier si la note a le label
                    if (!Note::isLabelExist($note->getId(), $label)) {
                        // Retirer la note du tableau $myNotes si elle n'a pas le label
                        $myNotes = array_filter($myNotes, function($n) use ($note) {
                            return $n !== $note;
                        });
                        // Sortir de la boucle foreach pour éviter de retirer la même note plusieurs fois
                        break;
                    }
                }
            }
        (new View("search_notes"))->show(["user" => $user, "myNotes" => $myNotes, "allSharedNotes" => $allSharedNotes,"labels"=>$labels, "encodedUrl" => $encodedUrl, "checkedLabels" => $checkedLabels]);
    }
    
    public function searchNotesJs() : void {
        $user = $this->get_user_or_redirect();
        $myNotes = [];
        $allSharedNotes = [];
        $this->data = $_POST["checkedLabels"];
    
        // Supposer que $_POST["checkedLabels"] est toujours défini et est un tableau
        $checkedLabels = $_POST["checkedLabels"] ?? [];
    
        foreach ($checkedLabels as $label) {
            $labelNotes = $user->getMyNotesByLabel($label);
            foreach($labelNotes as $note){
                if (!in_array($note, $myNotes)) {
                    $myNotes[] = $note;
                }
            }
            
            $allSharedNotes = array_merge($allSharedNotes, $user->getSharedNotesByLabel($label));
        }
    
        // Filtrer les notes qui ne correspondent plus aux étiquettes
        $myNotes = array_filter($myNotes, function($note) use ($checkedLabels) {
            foreach ($checkedLabels as $label) {
                if (!Note::isLabelExist($note->getId(), $label)) {
                    return false;
                }
            }
            return true;
        });
    
        $allSharedNotes = array_filter($allSharedNotes, function($note) use ($checkedLabels) {
            foreach ($checkedLabels as $label) {
                if (!Note::isLabelExist($note->getId(), $label)) {
                    return false;
                }
            }
            return true;
        });
    
        $notesArray = [];
        $encodedUrl = Tools::url_safe_encode($this->data);

        foreach ($myNotes as $note) {
            $items = [];
            if($note instanceof CheckListNote){
                foreach($note->getChecklistItems() as $item){
                    $items[] = [
                        'itemContent'=>$item->getContent(),
                        'checked'=>$item->isChecked(),
                    ];
                }
            }
            $notesArray[] = [
                'noteType'=>$note instanceof TextNote ? "TextNote" : "ChecklistNote",
                'noteId' => $note->getId(), 
                'title' => $note->getTitle(),
                'content' => $note instanceof TextNote ? $note->getTruncateContent() : $note->getChecklistItems(),
                'items'=> $items,
                'labels' => $note->getLabels(), 
                'encodedUrl'=>$encodedUrl,
            ];
        }
        $sharedNotes = [];

        foreach ($allSharedNotes as $note) {
            $sharedUser = User::getUserById($note->getOwnerId());
            $items = [];
            if($note instanceof CheckListNote){
                foreach($note->getChecklistItems() as $item){
                    $items[] = [
                        'itemContent'=>$item->getContent(),
                        'checked'=>$item->isChecked(),
                    ];
                }
            }
            $sharedNotes[] = [
                'sharedUserName'=> $sharedUser->getFullName(),
                'noteType'=>$note instanceof TextNote ? "TextNote" : "ChecklistNote",
                'noteId' => $note->getId(), 
                'title' => $note->getTitle(),
                'content' => $note instanceof TextNote ? $note->getTruncateContent() : $note->getChecklistItems(),
                'items'=> $items,
                'labels' => $note->getLabels(), 
                'encodedUrl'=>$encodedUrl,
            ];
        }
        
        // Renvoyer les données sous forme de JSON
        header('Content-Type: application/json');
        echo json_encode(["myNotes" => $notesArray, "allSharedNotes" => $sharedNotes]);
        exit; // Arrêter l'exécution après l'envoi des données
    }
    
    public function toggeCheckItems() : void {
        $checkedNotesIds = $_POST["checkedNotesIds"] ?? [];

        foreach($checkedNotesIds as $noteId) {
            $note = Note::getNoteById($noteId);
            foreach($note->getChecklistItems() as $item) {
                $item->checkItem(!$item->isChecked());
            }
        }

    }

    public function openNote() : void {
        $noteId = $this->getIdUrl();
        $note = Note::getNoteById($noteId);
        $dataUrl = isset($_GET['param2']) ? $_GET['param2'] : '';
        if ($note instanceof TextNote) {
            $this->redirect("note","openTextNote",$noteId, $dataUrl);
        }
        elseif ($note instanceof CheckListNote) {
            $this->redirect("note","openChecklistNote",$noteId, $dataUrl);
        }
    }
    public function editNote() : void {
        $noteId = $this->getIdUrl();
        $note = Note::getNoteById($noteId);
        $dataUrl = isset($_GET['param2']) ? $_GET['param2'] : '';
        if ($note instanceof TextNote) {
            $this->redirect("note","editTextNote",$noteId, $dataUrl);
        }
        elseif ($note instanceof CheckListNote) {
            $this->redirect("note","editChecklistNote",$noteId, $dataUrl);
        }
    }
    public function moveNote(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['direction']) && isset($_POST['note_id'])) {
            $noteId = $_POST['note_id'];
            $direction = $_POST['direction'];
    
            $note = Note::getNoteById($noteId);
    
            if ($note !== null) {
                $neighbourId = null;
                // Récupération de l'ID de la note voisine
                if ($direction === 'right'){
                    $neighbourId = $note->getRightNeighbourId();
                }
                elseif ($direction === 'left') {
                    $neighbourId = $note->getLeftNeighbourId();
                }
                if ($neighbourId !== null) {
                    // Effectuer le déplacement en ajustant les poids des notes
                    $note->swapWeights($noteId, $neighbourId);
                }
            }
        }
    
        $this->redirect("user","notes");
    }

    public function addTextNote() : void {
        $user = $this->get_user_or_redirect();
        $userId = $user->get_user_idFromDatabase();
        $currentDate = new DateTime();

        $title = [];
        $content = [];
        $errors = [];
        $errorContent = [];
        if (isset($_POST['notetitle'])) {
            $title = $_POST['notetitle'];
            $content = $_POST['noteContent'];

            $errors = Note::validateTitle($title);
            $errorContent = Note::validateContent($content);
            if (Note::isTitleExist($title, $userId)) {
                $errors[] = "Title must be unique";
            }

            if (empty($errors) && empty($errorContent)){
                $note = new TextNote(0,$title,$userId,$currentDate,0,0,1,null);
                $note->setWeight(max(Note::maxPinnedNotesWeight($userId), Note::maxOtherNotesWeight($userId))+1);
                if (!empty($content)){
                    $note->setContent($content);
                }
                $note->persist();
                $this->redirect("note","openTextNote" , $note->getId());
            }
        }
            
        (new View("add_text_note"))->show(["user" => $user, "errors" => $errors,"errorContent" => $errorContent]);
    }

    public function addCheckListNote(): void {
        $user = $this->get_user_or_redirect();
        $userId = $user->get_user_idFromDatabase();
        $currentDate = new DateTime();
        $errors = ['title' => [], 'item' => []];    
        $title = [];
        $items = [];
    
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Récupérer le titre
            $title = $_POST['notetitle'];
    
            // Vérifier la taille du titre
            $errors['title'] = Note::validateTitle($title);
            if (Note::isTitleExist($title, $userId)) {
                $errors['title'][] = "Title must be unique";
            }
    
            for ($i = 1; $i <= 5; $i++) {
                $itemName = 'item' . $i;
                if (isset($_POST[$itemName])) {
                    $item = $_POST[$itemName];
            
                    if (!empty($item)) {
                        // Vérifier si l'élément est déjà présent
                        if (in_array($item, $items)) {
                            $errors['item'][$i][] = 'Items must be unique';
                        } else {
                            $items[] = $item;
                        }
                    }
                }
            }       
    
            // S'il n'y a pas d'erreurs, créer la note
            if (empty($errors['title']) && empty($errors['item'])) {
                $note = new CheckListNote(0, $title, $userId, $currentDate, 0, 0, 1, null);
                $note->setWeight(max(Note::maxPinnedNotesWeight($userId), Note::maxOtherNotesWeight($userId))+1);
                $note->persist();
    
                if (!empty($items)) {
                    $note->setChecklistItems($items);
                }
                $this->redirect("note","openChecklistNote",  $note->getId());
            }
        }
    
        (new View("add_checklist_note"))->show(["user" => $user, "errors" => $errors]);
    }
    private function getIdUrl() : int{
        $noteId = isset($_GET['param1']) ? (int)$_GET['param1'] : null;
        return $noteId != null ? $noteId : -1;
    }

    public function openTextNote() : void {

        $user = $this->get_user_or_redirect();
        $noteId = $this->getIdUrl();

        $note = Note::getNoteById($noteId);
        if ($note !== null) {
            if ($note->getOwnerId() === $user->getId() || in_array($user,$note->getUsersSharedWith())){
                $creationDate = $note->getCreationDate();
                $editionDate = $note->getEditionDate();

                (new View("open_text_note"))->show(["note" => $note, "creationDate" => $creationDate, "editionDate" => $editionDate, "user" => $user]);
            }
        }
        else {
            $this->redirect("user","notes");
        }
    }

    public function openChecklistNote() : void {
        $encodedUrl = isset($_GET['param2']) ? $_GET['param2'] : '';

        $user = $this->get_user_or_redirect();
        $noteId = $this->getIdUrl();
        $note = CheckListNote::getNoteById($noteId);
        if ($note !== null) {
            if ($note->getOwnerId() === $user->getId() || in_array($user,$note->getUsersSharedWith())){
                $creationDate = $note->getCreationDate();
                $editionDate = $note->getEditionDate();
                $items = $note->getChecklistItems();
                (new View("open_checklist_note"))->show(["noteId" => $noteId,"note" => $note, "creationDate" => $creationDate, "editionDate" => $editionDate, "items" => $items, "user" => $user, "encodedUrl" => $encodedUrl]);
            }
        }
        else {
            $this->redirect("user","notes");
        }
    }

    public function checkUncheck() : void {
        $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : null;
        $encodedUrl = isset($_POST['encodedUrl']) ? $_POST['encodedUrl'] : '';

        $item = CheckListNoteItem::getItemById($itemId);
        $noteId = $item->getChecklistNoteId($itemId);
        $note = Note::getNoteById($noteId);
        $user = $this->get_user_or_redirect();

        if ($note->isSharedWithEditPerrmission($user->getId()) || $note->getOwnerId() == $user->getId()) {
            if ($itemId !== null) {
                $check = false;
                if ($item->isChecked() == true) {
                    $check = false;
                    $item->checkIem($check); 
                }
                else {
                    $check = true;
                    $item->checkIem($check);
                }
                $item->save($itemId, $check);
            } 
        }
        $this->redirect("note","openChecklistNote", $noteId, $encodedUrl);

    }
    public function checkUncheckJs() : void {
        $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : null;
    
        $item = CheckListNoteItem::getItemById($itemId);
        $noteId = $item->getChecklistNoteId($itemId);
        $note = Note::getNoteById($noteId);
        $user = $this->get_user_or_redirect();

        if ($note->isSharedWithEditPerrmission($user->getId()) || $note->getOwnerId() == $user->getId()) {
            if ($itemId !== null) {
                $check = !$item->isChecked(); // Toggle the checked status
                $item->checkIem($check);
                $item->save($itemId, $check);
        
                // Build data to return
                $responseData = array(
                    'item_id' => $itemId,
                    'checked' => $check
                );
        
                // Return JSON response
                header('Content-Type: application/json');
                echo json_encode($responseData);
                exit; // Terminate script after sending response
            } 
        }
        $this->redirect("note","openChecklistNote/".$noteId);
    }
    


    public function showMessageConfirm(): void{
        $user = $this->get_user_or_redirect();
        $noteId = $this->getIdUrl();
        $note = Note::getNoteById($noteId);
        if ($note !== null && $note->isArchived()) {
            if ($note->getOwnerId() === $user->getId()){
                (new View("message_de_confirmation_delete"))->show(["note"=>$note]);
            }
        }
        else {
            $this->redirect("user","archives");
        }
    }


    public function deleteNote(): void {
        $noteId = $this->getIdUrl();
        $note = Note::getNoteById($noteId);
        $note-> deleteNote();
        
        $this-> redirect("user","archives");
    }

    public function pin() {
        $userId = $this->get_user_or_redirect()->getId();
        $noteId = $this->getIdUrl();
        $note = Note :: getNoteById($noteId);
        $note -> pin();
        $note->setWeight(max(Note::maxPinnedNotesWeight($userId), Note::maxOtherNotesWeight($userId))+1);
        $note->updateWeight();
        $dataUrl = isset($_GET['param2']) ? $_GET['param2'] : '';
        $this -> redirect("note","openNote", $noteId , $dataUrl);
    }
    public function unPin() {
        $noteId = $this->getIdUrl();
        $note = Note :: getNoteById($noteId);
        $note -> unPin();
        $dataUrl = isset($_GET['param2']) ? $_GET['param2'] : '';
        $this -> redirect("note","openNote", $noteId , $dataUrl);
    }

    public function unarchive() {
        $noteId = $this->getIdUrl();
        $note = Note :: getNoteById($noteId);
        $note -> unarchive();
        $dataUrl = isset($_GET['param2']) ? $_GET['param2'] : '';
        $this -> redirect("note","openNote", $noteId , $dataUrl);
    }

    public function archive() {
        $noteId = $this->getIdUrl();
        $note = Note :: getNoteById($noteId);
        $note -> archive();
        $dataUrl = isset($_GET['param2']) ? $_GET['param2'] : '';
        $this -> redirect("note","openNote", $noteId , $dataUrl);
    }

    public function shareNote() : void {
        $user = $this->get_user_or_redirect();
        $dataUrl = isset($_GET['param2']) ? $_GET['param2'] : '';
        $noteId = $this->getIdUrl();
        $note = Note::getNoteById($noteId);
        $isSharedNote = NoteShare::isSharedNote($noteId);
        $users = User::get_users();
        if ($note !== null) {
            if ($note->getOwnerId() === $user->getId()){
                (new View("shares"))->show(["note" => $note,"dataUrl"=>$dataUrl , "isSharedNote" =>$isSharedNote , "users" =>$users] );
            }
        }
        else {
            $this->redirect("user","notes");
        }
    }

    public function addShare() : void {
        $noteId = isset($_POST['note_id']) ? (int)$_POST['note_id'] : null;
        $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;
        $permission = isset($_POST['permission']) ? $_POST['permission'] : null;
        $dataUrl = isset($_GET['param1']) ? $_GET['param1'] : '';

        $isUserDefault = ($userId === -1);
        $isPermissionDefault = ($permission === 'option1');
        
        if ($noteId != null && $userId != null && $permission != null && !$isUserDefault && !$isPermissionDefault){
            $editor = $permission == "editor";
            $noteShare = new NoteShare($userId, $noteId, $editor);
            $noteShare->addShare();
        }
        $this->redirect("note","shareNote", $noteId,$dataUrl);
    }
    public function addShareJS() : void {
        $noteId = isset($_POST['note_id']) ? (int)$_POST['note_id'] : null;
        $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;
        $permission = isset($_POST['permission']) ? $_POST['permission'] : null;
    
        if ($noteId != null && $userId != null && $permission != null && $userId !== -1 && $permission !== 'option1') {
            $editor = $permission === "editor";
            $noteShare = new NoteShare($userId, $noteId, $editor);
            $noteShare->addShare();
    
            // Récupérer la liste mise à jour des partages ET la liste des utilisateurs
            $response = [
                'shares' => $this->getSharesData($noteId),
                'availableUsers' => $this->getAvailableUsersJson($noteId)
            ];
            echo json_encode($response);
            return;
        }
        echo json_encode(['error' => 'Invalid input or operation failed']);
    }

    public function deleteShare() : void {
        $noteId = isset($_POST['note_id']) ? (int)$_POST['note_id'] : null;
        $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;
        $permission = isset($_POST['permission']) ? $_POST['permission'] : null;
        $dataUrl = isset($_GET['param1']) ? $_GET['param1'] : '';

        if ($noteId != null && $userId != null && $permission != null) {
            $editor = $permission == "editor";
            $noteShare = new NoteShare($userId, $noteId, $editor);
            $noteShare->deleteShare();
        }
        $this->redirect("note","shareNote", $noteId,$dataUrl);
    }
    public function deleteShareJS() : void {
        $noteId = isset($_POST['note_id']) ? (int)$_POST['note_id'] : null;
        $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;
        $permission = isset($_POST['permission']) ? $_POST['permission'] : null;

        if ($noteId != null && $userId != null && $permission != null) {
            $editor = $permission == "editor";
            $noteShare = new NoteShare($userId, $noteId, $editor);
            $noteShare->deleteShare();
    
            // Récupérer la liste mise à jour des partages ET la liste des utilisateurs
            $response = [
                'shares' => $this->getSharesData($noteId),
                'availableUsers' => $this->getAvailableUsersJson($noteId)
            ];
            echo json_encode($response);
            return;
        }
        echo json_encode(['error' => 'Invalid input or operation failed']);
    }

    public function togglePermission() : void {
        $noteId = isset($_POST['note_id']) ? (int)$_POST['note_id'] : null;
        $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;
        $permission = isset($_POST['permission']) ? $_POST['permission'] : null;
        $dataUrl = isset($_GET['param1']) ? $_GET['param1'] : '';

        if ($noteId != null && $userId != null && $permission != null) {
            $editor = $permission == "editor";
            $noteShare = new NoteShare($userId, $noteId, $editor);
            $noteShare->togglePermission();
        }
        $this->redirect("note","shareNote", $noteId,$dataUrl);
    }
    public function togglePermissionJS() : void {
        $noteId = isset($_POST['note_id']) ? (int)$_POST['note_id'] : null;
        $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;
        $permission = isset($_POST['permission']) ? $_POST['permission'] : null;
    
        if ($noteId != null && $userId != null && $permission != null) {
            $editor = $permission == "editor";
            $noteShare = new NoteShare($userId, $noteId, $editor);
            $noteShare->togglePermission();
    
            // Après modification des permissions, récupérer la liste mise à jour des partages
            $response = [
                'shares' => $this->getSharesData($noteId),
                'availableUsers' => $this->getAvailableUsersJson($noteId)
            ];
            echo json_encode($response);
            return;
        }
        echo json_encode(['error' => 'Invalid input or operation failed']);
    }
    
    private function getSharesData($noteId) {
        $note = Note::getNoteById($noteId); 
        $shares = [];
    
        if ($note) {
            // Obtenez tous les utilisateurs avec lesquels la note est partagée.
            $sharedUsers = $note->getUsersSharedWith(); //renvoie un tableau
    
            foreach ($sharedUsers as $sharedUser) {
                // Pour chaque utilisateur partagé, construisez un tableau de leurs données.
                $shares[] = [
                    'noteId' => $note->getId(),
                    'userId' => $sharedUser->getId(), 
                    'name' => $sharedUser->getFullName(), 
                    'permission' => $note->isSharedWithEditPerrmission($sharedUser->getId()) ? 'editor' : 'reader' 
                ];
            }
        }
        return $shares;
    }
    private function getAvailableUsersJson($noteId) {
        $note = Note::getNoteById($noteId);
        if (!$note) {
            return [];
        }
        $allUsers = User::get_users();
        $usersSharedWith = $note->getUsersSharedWith();
    
        $availableUsers = [];
        foreach ($allUsers as $user) {
            $isShared = false;

            foreach ($usersSharedWith as $sharedUser) {
                if ($user->get_user_idFromDatabase() == $sharedUser->get_user_idFromDatabase()) {
                    $isShared = true;
                    break;
                }
            }

            $isOwner = ($user->get_user_idFromDatabase() == $note->getOwnerId());
            if (!$isShared && !$isOwner) {
                $availableUsers[] = [
                    'id' => $user->getId(),
                    'name' => $user->getFullName()
                ];
            }
        }
    
        return $availableUsers;
    }
    
    public function editTextNote(){
        $encodedUrl = isset($_GET['param2']) ? $_GET['param2'] : '';
        $user = $this->get_user_or_redirect();
        $noteId = $this->getIdUrl();
        $note = TextNote::getNoteById($noteId);
        if ($note != null) {
            $creationDate = $note->getCreationDate();
            $editionDate = $note->getEditionDate();
            $errors = [];
        
            if(isset($_POST['editedTitle']) && isset($_POST['editedContent']) ){
                $title = $_POST['editedTitle'];
                $content =$_POST['editedContent'];
                /*if (Tools::sanitize($title) < 3 || Tools::sanitize($title) > 25) {
                    $errors[] = 'title length must be between 3 and 25';
                }*/
                $errors= Note::validateTitle($title);
                if ($note->getTitle() != $title && Note::isTitleExist($title, $user->getId())) {
                    $errors[] = "Title must be unique";
                }                
                if (empty($errors)){                   
                    $note->update($title, $content);
                    $this->redirect("note", "openNote", $noteId, $encodedUrl);
                }
            }
        
            if ($note->getOwnerId() === $user->getId() || $note->isSharedWithEditPerrmission($user->getId())){
                (new View("edit_text_note"))->show(["note"=>$note,"creationDate"=>$creationDate,"editionDate" => $editionDate,"errors"=> $errors, "encodedUrl" => $encodedUrl]);
            }
        }

    }
    
    public function editChecklistNote() {
        $encodedUrl = isset($_GET['param2']) ? $_GET['param2'] : '';
        $noteId = $this->getIdUrl(); // retrieve the note ID from the URL
        $note = CheckListNote::getNoteById($noteId); // get the note object
        $user = $this->get_user_or_redirect(); // get the logged-in user
        
        // Initialize variables
        $creationDate = $note->getCreationDate(); // get the creation date of the note
        $editionDate = $note->getEditionDate(); // get the last edition date of the note
        $itemsList = $note->getChecklistItems(); // get the checklist items
    
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $titleErrors = []; // initialize an array to store title errors
            $itemsErrors = [];
            $uniqueItems = []; // array to track unique items
    
            // Update title
            $title = $_POST['editedTitle'] ?? '';
    
            // Validate title length
            $titleErrors = Note::validateTitle($title);
            // Check title uniqueness
            if ($note->getTitle() != $title && Note::isTitleExist($title, $user->getId())) {
                $titleErrors[] = "Title must be unique";
            }  
    
            // Temporarily store checklist item contents for uniqueness check
            $itemContents = [];
    
            // Process checklist items
            foreach ($itemsList as $item) {
                $itemId = $item->getId();
                $itemContent = $_POST['content'.$itemId] ?? '';
    
                // Validate item content length
                $itemErrors = CheckListNoteItem::validateItemContent($itemContent);
    
                // Check item uniqueness within the form submission
                if (!empty($itemContent)) {
                    if (in_array($itemContent, $uniqueItems)) {
                        $itemErrors[] = "Items must be unique";
                    } else {
                        $uniqueItems[] = $itemContent;
                    }
                }
    
                if (!empty($itemErrors)) {
                    $itemsErrors[$itemId] = $itemErrors;
                }
            }
    
            // If no errors, proceed with database update
            if (empty($titleErrors) && empty($itemsErrors)) {
                $note->update($title);
    
                foreach ($itemsList as $item) {
                    $itemId = $item->getId();
                    $itemContent = $_POST['content'.$itemId] ?? '';
                    $item->updateContent($itemContent);
                }
    
                // Redirect to the note page
                $this->redirect("note", "openChecklistNote", $noteId, $encodedUrl);
            } else {
                // Show the view with errors
                (new View("edit_checklist_note"))->show([
                    "note" => $note, 
                    "itemsErrors" => $itemsErrors, 
                    "titleErrors" => $titleErrors, 
                    "creationDate" => $creationDate, 
                    "editionDate" => $editionDate, 
                    "itemsList" => $itemsList, 
                    "encodedUrl" => $encodedUrl
                ]);
            }
        } else {
            // If not a form submission, show the edit page normally
            (new View("edit_checklist_note"))->show([
                "note" => $note, 
                "creationDate" => $creationDate, 
                "editionDate" => $editionDate, 
                "itemsList" => $itemsList, 
                "encodedUrl" => $encodedUrl
            ]);
        }
    }
    
   /* public function editChecklistNote() {
        $encodedUrl = isset($_GET['param2']) ? $_GET['param2'] : '';
        $noteId = $this->getIdUrl(); // récupère l'ID de la note
        $note = CheckListNote::getNoteById($noteId); // Récupérer l'objet note
        $user = $this->get_user_or_redirect(); // Récupérer l'utilisateur connecté
    
        // Initialisation des variables
        $creationDate = $note->getCreationDate(); // Obtener la date de création de la note
        $editionDate = $note->getEditionDate(); // Obtener la date de dernière édition de la note
        $itemsList = $note->getChecklistItems(); // Obtener les éléments de la liste de contrôle
        $contents = [];
        $originalContents = [];
        foreach ($itemsList as $item) {
            $contents[] = $item->getContent();
        }
        $originalContents = $contents;
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $titleErrors = []; // Initialiser un tableau pour stocker les éventuelles erreurs
            $itemsErrors = [];
    
            // Mise à jour du titre
            $title = $_POST['editedTitle'] ?? '';
    
            // Vérifier la taille du titre
            $titleErrors = Note::validateTitle($title);
            // Vérifier l'unicité du titre
            if ($note->getTitle() != $title && Note::isTitleExist($title, $user->getId())) {
                $titleErrors[] = "Title must be unique";
            }  
    
            if (empty($titleErrors)) {
                $note->update($title); 
            } 
    
            // Stockage temporaire des contenus des éléments de la liste de contrôle pour vérification d'unicité
            $itemContents = [];
            $itemContents = $contents;
            var_dump($itemContents);
    
            // Traitement des éléments de la liste de contrôle
            foreach ($itemsList as $item) {
                $itemId = $item->getId();
                $itemContent = $_POST['content'.$itemId] ?? '';
                
                // Vérification de la longueur du contenu
                $itemsErrors[$itemId] = CheckListNoteItem::validateItemContent($itemContent);
    
                // Vérification de l'unicité de l'élément
                if (!empty($itemContent)) {
                    if (in_array($itemContent,$itemContents)) {
                        $itemsErrors[$itemId][] = "Items must be unique";
                    } else {
                        $itemContents[$itemContent] = true;
                    }
                    
                   // if (in_array($itemContent, $contents)) {
                        //$itemsErrors[$itemId][] = "Items must be unique";
                    //}
                    //else {
                        //$contents[] = $itemContent;
                    //}

    
                }
    
                // Si aucune erreur, mettre à jour l'élément de la liste de contrôle
                if (empty($itemsErrors[$itemId])) {
                    $item->updateContent($itemContent);
                }
            }
    
            // Rediriger l'utilisateur si tout est correct, sinon afficher les erreurs
            if (empty(array_filter($itemsErrors)) && empty($titleErrors)) {
                // Redirection vers la page de la note
                $this->redirect("note", "openChecklistNote", $noteId, $encodedUrl);
            } else {
                // Afficher la vue avec les erreurs
                (new View("edit_checklist_note"))->show(["note" => $note, "itemsErrors" => $itemsErrors, "titleErrors" => $titleErrors, "creationDate" => $creationDate, "editionDate" => $editionDate, "itemsList" => $itemsList, "encodedUrl" => $encodedUrl]);
            }
        } else {
            // Si ce n'est pas une soumission de formulaire, afficher la page d'édition normalement
            (new View("edit_checklist_note"))->show(["note" => $note, "creationDate" => $creationDate, "editionDate" => $editionDate, "itemsList" => $itemsList, "encodedUrl" => $encodedUrl]);
        }

    }*/

    public function addItemJs() : void {
        $encodedUrl = isset($_POST['param2']) ? $_POST['param2'] : '';
        $noteId = $this->getIdUrl();
        $content = isset($_POST['content']) ? $_POST['content'] : null;
        $note = CheckListNote::getNoteById($noteId);
        $item = CheckListNoteItem::getItemByName($content, $noteId);
        $newItemErrors = [];

        // Vérification de la longueur du contenu
        $newItemErrors = CheckListNoteItem::validateItemContent($content);
    
        // Vérification si l'item est déjà dans la liste
        if ($item != null) {
            $newItemErrors[] = "Items must be unique";
        }
    
        // Si aucune erreur, ajout du nouvel élément
        if (empty($newItemErrors)){
            $newItem = new CheckListNoteItem(0, $noteId, $content, false);
            $newItem->addNewItem();
            $itemm = CheckListNoteItem::getItemByName($newItem->getContent(), $noteId);

            $response = [
                'noteId' =>$noteId,
                'encodedUrl' => $encodedUrl,
                'content' => $newItem->getContent(),
                'checked' => $newItem->isChecked(),
                'id' =>$itemm->getId()
            ];
            echo json_encode($response);
            return;        
        }     
    }
    
    public function addItem() {
        $encodedUrl = isset($_GET['param2']) ? $_GET['param2'] : '';
        $noteId = $this->getIdUrl();
        $content = isset($_POST['content']) ? $_POST['content'] : null;
        $note = CheckListNote::getNoteById($noteId);
        $item = CheckListNoteItem::getItemByName($content, $noteId);
        $newItemErrors = [];

        // Initialisation des variables
        $creationDate = $note->getCreationDate(); 
        $editionDate = $note->getEditionDate(); 
        $itemsList = $note->getChecklistItems();
    
        // Vérification de la longueur du contenu
        $newItemErrors = CheckListNoteItem::validateItemContent($content);
    
        // Vérification si l'item est déjà dans la liste
        if ($item != null) {
            $newItemErrors[] = "Items must be unique";
        }
    
        // Si aucune erreur, ajout du nouvel élément
        if (empty($newItemErrors)){
            $newItem = new CheckListNoteItem(0, $noteId, $content, false);
            $newItem->addNewItem();
            $this->redirect("note", "editCheckListNote", $noteId, $encodedUrl);
        }
        else
            (new View("edit_checklist_note"))->show(["note" => $note, "newItemErrors" => $newItemErrors, "creationDate" => $creationDate, "editionDate" => $editionDate, "itemsList" => $itemsList]);

        
    }
    
    public function deleteItem() : void {
        $encodedUrl = isset($_GET['param3']) ? $_GET['param3'] : '';
        $noteId = $this->getIdUrl();//id de la note
        $itemId = isset($_GET["param2"]) ? $_GET["param2"] : null;
        $item = CheckListNoteItem::getItemById($itemId);
        $item->deleteItem();
        $this->redirect("note","editCheckListNote", $noteId, $encodedUrl);

    }

    public function note_exists_service() : void {
        $res = "false";
        $title = $_POST['title'];
        $userId = $this->get_user_or_redirect()->getId();
        $note = Note::getNoteByTitle($title , $userId);
        if($note)
            $res =  "true"; 

        echo $res;
    }
    public function item_exists_service() : void {
        $res = "false";
        
        $itemValue = $_POST['itemValue'];
        $noteId = $_POST['noteId'];
        $item = CheckListNoteItem::getItemByName($itemValue, $noteId);
        if($item) 
            $res = "true";
    
        echo $res;
    }

   // Méthode pour mettre à jour les poids des notes après le déplacement
    public function updateNoteWeights() {
        $userId = $this->get_user_or_redirect()->getId();
        $sortedNoteIds = $_POST['sortedIDs']; // Récupérer les identifiants triés depuis la requête POST
        $sortedNoteIds = array_reverse($sortedNoteIds);

        // Récupérer la zone de dépôt depuis la requête POST
        $dropzone = $_POST['dropzone'];
        //$dropzone === "pinned-notes" ? $initialWeight = Note::maxPinnedNotesWeight($userId)+1 : $initialWeight = Note::maxOtherNotesWeight($userId)+1;
        $initialWeight = max(Note::maxPinnedNotesWeight($userId), Note::maxOtherNotesWeight($userId))+1;

        // Vérifier la valeur de la zone de dépôt
        if($dropzone === "pinned-notes" || $dropzone === "other-notes") {
            foreach ($sortedNoteIds as $noteId) {
                // Récupérer l'instance de la note par son ID
                $note = Note::getNoteById($noteId);
                
                if ($note) {
                    // Mettre à jour le poids de la note dans la base de données
                    $note->setWeight($initialWeight);
                    $note->updateWeight();
                    $initialWeight++; // Incrémenter le poids pour la prochaine note
                    if ($dropzone === "pinned-notes") {
                        $note->pin();
                    } else if ($dropzone === "other-notes") {
                        $note->unPin();
                    }
                }
            }
        }
    }

    public function deleteNoteJs(): void {
        $noteId = $_POST['noteId'];
        $note = Note::getNoteById($noteId);
        $note-> deleteNote();
        
        $this-> redirect("user","notes");
    }
    
}