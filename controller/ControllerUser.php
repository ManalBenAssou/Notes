<?php


require_once 'model/User.php';
require_once 'model/TextNote.php';
require_once 'model/CheckListNote.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';


class ControllerUser extends Controller {
   
    public function index() : void {

        $this->notes();
    }


    public function session1() : void {
        $user = $this->get_user_or_redirect();
        $users = User::get_users();

        if (isset($_POST["user_id"])) {
            $userId = $_POST["user_id"];

            $this->redirect("user", "displayUserChecklistNotes", $userId);
        }
        else
            (new View("session1"))->show(["users" => $users]);
    }

    public function displayUserChecklistNotes() : void {
        $users = User::get_users();
        $userId = isset($_GET["param1"]) ? $_GET["param1"] : null;
        $user = User::getUserById($userId);

        $userChecklistNotes = [];
        $checkedNotes = $user->getChecklistNotes();

        foreach($checkedNotes as $note) {
            if (!in_array($note, $userChecklistNotes))
                $userChecklistNotes[] = $note;
        }
        
        (new View("session1"))->show(["users" => $users, "userChecklistNotes" => $userChecklistNotes]);

    }

    public function settings() : void {
        $user = $this->get_user_or_redirect();
        (new View("settings"))->show(["user" => $user]);
    } 

    public function notes() : void {
        $user = $this->get_user_or_redirect();
        $notes = $user->get_notes();
        (new View("notes"))->show(["user" => $user, "notes" => $notes]);
    }

    public function edit_profile() : void {
        $user = $this-> get_user_or_redirect();
        $errors = [];
        $newEmail = '';
        $newUsername = '';

        if (isset($_POST['username']) && isset($_POST['email'])) {
            $newUsername = $_POST['username'];
            $UsernameErrors= User:: validate_fullName($newUsername);  
            $newEmail = $_POST['email'];
            $emailErrors = User:: validate_email($newEmail); 
            if ($newEmail !== $user->getMail())
                $emailErrors = User::validate_unicity($newEmail);

            $errors = array_merge($UsernameErrors,$emailErrors);
            
            if (count($errors) == 0) {
                $user->save_profile($newEmail,$newUsername);
                $user -> setUsername($newUsername);
                $user -> setMail($newEmail);
                //$user->persist();
                $this->redirect("user","settings");
            }
        }

        (new View("edit_profile"))->show(["newUsername"=>$user -> getFullName(),"newEmail"=> $user -> getMail(), "errors" => $errors]);
    }
    public function archives() : void {
        $user = $this->get_user_or_redirect();
        $notes = $user->get_notes();
        (new View("archives"))->show(["user" => $user, "notes" => $notes]);

    }


    public function change_password() : void {

        $currentPassword = ""; 
        $newPassword = "";
        $confirmPassword = "";
        $user = $this-> get_user_or_redirect();
        $errors = [];
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (isset($_POST["currentPassword"]) && isset($_POST["newPassword"]) && isset($_POST["confirmPassword"])) {
                // Récupération des valeurs des champs
                $currentPassword = $_POST["currentPassword"];
                $newPassword = $_POST["newPassword"];
                $confirmPassword = $_POST["confirmPassword"];
                


                // Vérification du mot de passe actuel (ex: validation dans une base de données)
                if (!User::check_password($currentPassword,$user->getPassword())) {
                    $errors[] = "Le mot de passe actuel est incorrect.";
                }

                // Vérification du nouveau mot de passe
                $newPasswordErrors = User::validate_password($newPassword);
                $confirmPasswordErrors = User::validate_passwords($newPassword, $confirmPassword);

                // Combinaison des messages d'erreur
                $errors = array_merge($errors, $newPasswordErrors, $confirmPasswordErrors);
                
                if(empty($errors)){
                // $user->setPassword($newPassword);
                    $user->save($newPassword);
                    $this->redirect("user","notes");

                }
            }
        }
        (new View("change_password"))->show(["hashed_Password"=>$currentPassword, "newPassword" => $newPassword,"confirmpassword" => $confirmPassword, "errors" => $errors]);
    }
    public function sharedNotes() : void {
        $user = $this->get_user_or_redirect();
        $sharedUserId = isset($_GET['param1']) ? $_GET['param1'] : null;


        if ($sharedUserId !== null) {
        $sharedUser = User::getUserById($sharedUserId);
        $notesWithEditPermission = $user->getNotesSharedAsEditor($sharedUserId);
        $notesWithoutEditPermission = $user->getNotesSharedAsReader($sharedUserId);


        (new View("shared_notes"))->show(["user" => $user, "sharedUser"=> $sharedUser, 
        "notesWithEditPermission" => $notesWithEditPermission, "notesWithoutEditPermission" => $notesWithoutEditPermission]);
        }
    }
}
