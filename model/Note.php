<?php
require_once "framework/Model.php";
require_once "User.php";
require_once "TextNote.php";
require_once "CheckListNote.php";



abstract class Note extends Model {
        protected int $id; 
        protected string $title; 
        private int $owner; 
        private DateTime $created_at;
        private bool $pinned ;
        private bool $archived;
        private int $weight;
        protected ?DateTime $edited_at;

    public function __construct(
        int $id, 
        string $title, 
        int $owner, 
        DateTime $created_at, 
        bool $pinned, 
        bool $archived, 
        int $weight, 
        ?DateTime $edited_at
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->owner = $owner;
        $this->created_at = $created_at;
        $this->pinned = $pinned;
        $this->archived = $archived;
        $this->weight = $weight;
        $this->edited_at = $edited_at;

    }
    public function dateFormat($datetime) {
        if ($datetime instanceof DateTime) {
            $datetime = $datetime->format('Y-m-d H:i:s');
         }
        $timeAgo = strtotime($datetime);
        $currentTime = time();
        $timeDifference = $currentTime - $timeAgo;
        $seconds = $timeDifference;
        $minutes = round($seconds / 60);
        $hours = round($seconds / 3600);
        $days = round($seconds / 86400);
        $weeks = round($seconds / 604800);
        $months = round($seconds / 2629440);
        $years = round($seconds / 31553280);

        if ($seconds <= 60) {
            return "just now";
        } else if ($minutes <= 60) {
            return $minutes == 1 ? "one minute ago" : "$minutes minutes ago";
        } else if ($hours <= 24) {
            return $hours == 1 ? "an hour ago" : "$hours hours ago";
        } else if ($days <= 7) {
            return $days == 1 ? "yesterday" : "$days days ago";
        } else if ($weeks <= 4.3) {
            return $weeks == 1 ? "a week ago" : "$weeks weeks ago";
        } else if ($months <= 12) {
            return $months == 1 ? "a month ago" : "$months months ago";
        } else {
            return $years == 1 ? "one year ago" : "$years years ago";
        }
    }

    // Méthode abstraite pour récupérer le contenu spécifique de chaque type de note
    abstract public function getContent() : ?string;

    public function getId(): int {
        return $this->getNoteIdFromDatabase();
    }
    public function getOwnerId() : int {
        return $this->owner;
    }
    public function getTitle() : string {
        return $this->title;
    }
    public function getCreationDate() : DateTime {
        return $this->created_at;
    }
    public function isPinned() : bool {
        return $this->pinned;
    }
    public function isArchived() : bool {
        return $this->archived;
    }
    public function getWeight() : int {
        return $this->weight;
    }
    public function getEditionDate() : ?DateTime {
        return $this->edited_at;
    }

    public function setWeight(int $weight) {
        $this->weight = $weight;
    }

    public function getNoteIdFromDatabase() : int {
        $query = self::execute("SELECT id FROM notes WHERE title = :title", ["title" => $this->title]);
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return 0;
        } else {
            return $data['id'];
        }
    }

    public static function getAllUserLabels(int $userId) : array {
        $query = self::execute("SELECT DISTINCT label FROM note_labels, notes WHERE note_labels.note = notes.id AND notes.owner = :userId"
        , ["userId" => $userId]);
        $data = $query->fetchAll();
        $labels = [];

        foreach($data as $label) {
            $labels[] = $label["label"]; 
        }
        
        return $labels;
    }
    public function getLabels() : bool|array {
        $query = self::execute("SELECT label FROM note_labels WHERE note = :noteId", ["noteId" => $this->getId()]);
        $data = $query->fetchAll();
        $labels = [];

        if ($query->rowCount() == 0) {
            return false;

        } 
        foreach($data as $label) {
        $labels[] = $label["label"]; 
        }
        
        return $labels;
        
    }
    
    public static function getChecklistNotesByUser($ownerId) : array {
        if ($ownerId === 0) {
            // Gérer le cas où l'ID de l'utilisateur n'est pas trouvé
            return [];
        }

        $query = self::execute("SELECT * FROM notes WHERE owner = :ownerId ORDER BY weight DESC", ["ownerId" => $ownerId]);
        $data = $query->fetchAll();
        $notes = [];

        $note = null;
        foreach ($data as $row) {
            $created_at = new DateTime($row['created_at']);
            $edited_at = !empty($row['edited_at']) ? new DateTime($row['edited_at']) : null;
            
                $noteType = Note::getNoteTypeById($row['id']);
               if ($noteType === 'CheckListNote') {
                    $note = new CheckListNote(
                        $row['id'],
                        $row['title'],
                        $row['owner'],
                        $created_at,
                        $row['pinned'],
                        $row['archived'],
                        $row['weight'],
                        $edited_at,
                        
                    );
                }
            
            
            $notes[] = $note;
        }

        return $notes;
        
    }
    public static function getNotesByUser($ownerId) : array {
        if ($ownerId === 0) {
            // Gérer le cas où l'ID de l'utilisateur n'est pas trouvé
            return [];
        }

        $query = self::execute("SELECT * FROM notes WHERE owner = :ownerId ORDER BY weight DESC", ["ownerId" => $ownerId]);
        $data = $query->fetchAll();
        $notes = [];

        $note = null;
        foreach ($data as $row) {
            $created_at = new DateTime($row['created_at']);
            $edited_at = !empty($row['edited_at']) ? new DateTime($row['edited_at']) : null;
            
                $noteType = Note::getNoteTypeById($row['id']);
                if ($noteType === 'TextNote') {
                    $note = new TextNote(
                        $row['id'],
                        $row['title'],
                        $row['owner'],
                        $created_at,
                        $row['pinned'],
                        $row['archived'],
                        $row['weight'],
                        $edited_at
                        
                    );
                } elseif ($noteType === 'CheckListNote') {
                    $note = new CheckListNote(
                        $row['id'],
                        $row['title'],
                        $row['owner'],
                        $created_at,
                        $row['pinned'],
                        $row['archived'],
                        $row['weight'],
                        $edited_at,
                        
                    );
                }
            
            
            $notes[] = $note;
        }

        return $notes;
        
    }
    public static function isLabelExist($noteId , $label) {
        $query = self::execute("SELECT COUNT(*) as count FROM note_labels WHERE note = :noteId and  label = :label "  , ["noteId" => $noteId , "label" => $label]);
        $data = $query->fetch();
    
        return $data !== false && (int)$data['count'] > 0;
    } 
    public static function validateLabel(string $label) : array {
        $errors = [];
            if (mb_strlen(Tools::sanitize($label)) < Configuration::get("label_min_length") || mb_strlen(Tools::sanitize($label)) > Configuration::get("label_max_length")){
                $errors [] = "Label length must be between 2 and 10";
            }
        return $errors;
    }
    public function deleteLabel(string $label) {
        self::execute("DELETE FROM note_labels WHERE note = :noteId AND label = :label",
        ["noteId" => $this->getId(), "label" => $label]);
    }
    public function addLabel(string $newLabel) {
        self::execute("INSERT INTO note_labels(note, label) VALUES(:noteId, :newLabel)",
        ["noteId" => $this->getId(), "newLabel" => $newLabel]);
    }
    
    public static function getNoteTypeById(int $noteId): string {
        $textNoteQuery = self::execute("SELECT id FROM text_notes WHERE id = :noteId", ["noteId" => $noteId]);
        $checklistNoteQuery = self::execute("SELECT id FROM checklist_notes WHERE id = :noteId", ["noteId" => $noteId]);

        if ($textNoteQuery->rowCount() > 0) {
            return 'TextNote';
        } elseif ($checklistNoteQuery->rowCount() > 0) {
            return 'CheckListNote';
        } else {
            
            return 'UnknownNoteType';
        }
    }

    public function getMinWeight() : int {
        if ($this->pinned) {
            $query = self::execute(
                "SELECT MIN(weight) as min_weight 
                 FROM notes 
                 WHERE owner = :owner AND pinned = 1 AND archived = 0",
                ["owner" => $this->owner]
            );
        } else {
            $query = self::execute(
                "SELECT MIN(weight) as min_weight 
                 FROM notes 
                 WHERE owner = :owner AND pinned = 0 AND archived = 0",
                ["owner" => $this->owner]
            );
        }
    
        $data = $query->fetch();
    
        return $data !== false ? (int)$data['min_weight'] : 0;
    }
    

    public function getMaxWeight() : int {
        if ($this->pinned) {
            $query = self::execute(
                "SELECT MAX(weight) as max_weight 
                 FROM notes 
                 WHERE owner = :owner AND pinned = 1 AND archived = 0",
                ["owner" => $this->owner]
            );
        } else {
            $query = self::execute(
                "SELECT MAX(weight) as max_weight 
                 FROM notes 
                 WHERE owner = :owner AND pinned = 0 AND archived = 0",
                ["owner" => $this->owner]
            );
        }
    
        $data = $query->fetch();
    
        return $data !== false ? (int)$data['max_weight'] : 0;
    }
    public static function getNoteByTitle($title,$userId){
        $query = self::execute("SELECT id as noteId FROM notes WHERE title = :title AND owner = :userId",
        ["title" => $title,"userId" => $userId]);
        $data = $query->fetch();

        if($query -> rowCount() == 0){
            return null;// Aucune note trouvée avec ce titre
        }
        return self::getNoteById($data['noteId']);
        
    }

    public static function getNoteById($id) {
        $query = self::execute("SELECT * FROM notes WHERE id = :id", ["id" => $id]);
        $data = $query->fetch();

        if ($query->rowCount() == 0) {
            return null; // Aucune note trouvée avec cet ID
        }

        $note = null;
        $created_at = new DateTime($data['created_at']);
        $edited_at = !empty($data['edited_at']) ? new DateTime($data['edited_at']) : null;
        // Créer une instance de Note et la retourner
        if (Note::getNoteTypeById($id) === 'TextNote') {
            $note = new TextNote(
            $data['id'],
            $data['title'],
            $data['owner'],
            $created_at,
            (bool)$data['pinned'],
            (bool)$data['archived'],
            $data['weight'],
            $edited_at
        );
        } elseif (Note::getNoteTypeById($id) === 'CheckListNote') {
            $note = new CheckListNote(
                $data['id'],
                $data['title'],
                $data['owner'],
                $created_at,
                (bool)$data['pinned'],
                (bool)$data['archived'],
                $data['weight'],
                $edited_at
            );
        }
        return $note;
    }

    public function persist() {
        // Formater la date/heure ici avant de l'utiliser dans la requête SQL
        $formattedCreatedAt = $this->created_at->format('Y-m-d H:i:s');
        // Convertir la valeur de "pinned" en un entier (0 ou 1)
        $pinnedValue = $this->pinned ? 1 : 0;
        $archivedValue = $this->archived ? 1 : 0;


        if ($this->id) {
            // La note existe déjà, donc effectuer une mise à jour
            self::execute("UPDATE notes SET title = :title, owner = :owner, created_at = :created_at,
                                      pinned = :pinned, archived = :archived, weight = :weight,
                                      edited_at = :edited_at WHERE id = :id",
                          [
                              "id" => $this->id,
                              "title" => $this->title,
                              "owner" => $this->owner,
                              "created_at" => $formattedCreatedAt,
                              "pinned" => $pinnedValue,
                              "archived" => $archivedValue,
                              "weight" => $this->weight,
                              "edited_at" => $this->edited_at
                          ]
            );
        }
        else {

        self::execute("INSERT INTO notes(title, owner, created_at, pinned, archived, weight, edited_at) 
                      VALUES(:title, :owner, :created_at, :pinned, :archived, :weight, :edited_at)", 
                      [
                          "title" => $this->title,
                          "owner" => $this->owner,
                          "created_at" => $formattedCreatedAt,
                          "pinned" => $pinnedValue,
                          "archived" => $archivedValue,
                          "weight" => $this->weight,
                          "edited_at" => $this->edited_at
                      ]
        );
    }
    }

    public static function validateTitle(string $title) : array {
        $errors = [];
            if (strlen(Tools::sanitize($title)) < Configuration::get("title_min_length") || strlen(Tools::sanitize($title)) > Configuration::get("title_max_length")){
                $errors [] = "Title length must be between 3 and 25";
            }
        return $errors;
    }
    public static function validateContent(string $content) : array {
        $errors = [];
            if (strlen($content) > Configuration::get("content_max_lenght")){
                $errors [] = "Content length must be less than 2000";
            }
        return $errors;
    }
    public static function isTitleExist($title , $owner) {
        //self::execute("SELECT title from notes where title = :title",);
        $query = self::execute("SELECT COUNT(*) as count FROM notes WHERE title = :title and  owner = :owner "  , ["title" => $title , "owner" => $owner]);
        $data = $query->fetch();
    
        return $data !== false && (int)$data['count'] > 0;
    }

    public function getRightNeighbourId() {
        $neighbourWeight = $this->getRightNeighbourWeight();
        $query = self::execute("SELECT id from notes where owner = :owner AND weight = :neighbourWeight",
        ["owner" => $this->owner, "neighbourWeight" => $neighbourWeight]);
        $data = $query->fetch();

        return $data ? $data['id'] : null;
    }

    private function getRightNeighbourWeight() {
        $myNoteId = $this->getId();
        $myNoteWeight = Note::getNoteById($myNoteId)->getWeight();

        if ($this->pinned) {
            $query = self::execute(
                "SELECT MAX(weight) as max_weight 
                FROM notes 
                WHERE owner = :owner AND pinned = 1 AND archived = 0 and weight < :myNoteWeight",
                ["owner" => $this->owner, "myNoteWeight" => $myNoteWeight]
            );
        } else {
            $query = self::execute(
                "SELECT MAX(weight) as max_weight 
                FROM notes 
                WHERE owner = :owner AND pinned = 0 AND archived = 0 and weight < :myNoteWeight",
                ["owner" => $this->owner, "myNoteWeight" => $myNoteWeight]
            );
        }

        $data = $query->fetch();

        return $data !== false ? (int)$data['max_weight'] : 0;
    }
    public function getLeftNeighbourId() {
        $neighbourWeight = $this->getLeftNeighbourWeight();
        $query = self::execute("SELECT id from notes where owner = :owner AND weight = :neighbourWeight",
        ["owner" => $this->owner, "neighbourWeight" => $neighbourWeight]);
        $data = $query->fetch();

        return $data ? $data['id'] : null;
    }
    private function getLeftNeighbourWeight() {
        $myNoteId = $this->getId();
        $myNoteWeight = Note::getNoteById($myNoteId)->getWeight();

        if ($this->pinned) {
            $query = self::execute(
                "SELECT MIN(weight) as min_weight 
                FROM notes 
                WHERE owner = :owner AND pinned = 1 AND archived = 0 and weight > :myNoteWeight",
                ["owner" => $this->owner, "myNoteWeight" => $myNoteWeight]
            );
        } else {
            $query = self::execute(
                "SELECT MIN(weight) as min_weight 
                FROM notes 
                WHERE owner = :owner AND pinned = 0 AND archived = 0 and weight > :myNoteWeight",
                ["owner" => $this->owner, "myNoteWeight" => $myNoteWeight]
            );
        }

        $data = $query->fetch();

        return $data !== false ? (int)$data['min_weight'] : 0;
    }

    public function swapWeights($noteId, $neighbourId) {
        $note = $this->getNoteById($noteId);
        $noteWeight = $note->getWeight();
        
        $neighbour = $this->getNoteById($neighbourId);
        $neighbourWeight = $neighbour->getWeight();
        self::execute("UPDATE notes SET weight = :neighbourWeight WHERE id = :noteId", ["noteId" => $noteId, "neighbourWeight" => $neighbourWeight+0.5]);
        self::execute("UPDATE notes SET weight = :noteWeight WHERE id = :neighbourId", ["neighbourId" => $neighbourId, "noteWeight" => $noteWeight+0.5]);

        self::execute("UPDATE notes SET weight = :neighbourWeight WHERE id = :noteId", ["noteId" => $noteId, "neighbourWeight" => $neighbourWeight]);
        self::execute("UPDATE notes SET weight = :noteWeight WHERE id = :neighbourId", ["neighbourId" => $neighbourId, "noteWeight" => $noteWeight]);
    }

    public function getNoteType() {
        if ($this->archived == 1) {
            return 'archived';
        } elseif ($this->isShared($this->id)) {
            return 'shared';
        } else {
            return 'normal';
        }
    }
    public function isShared() : bool {
        $noteId = $this->id;
        $query = self::execute("SELECT COUNT(*) as count FROM note_shares WHERE note = :noteId AND user <> :owner", ["noteId" => $noteId, "owner" =>$this->owner]);
        $data = $query->fetch();
    
        return $data !== false && (int)$data['count'] > 0;
    }

    public function getUsersSharedWith() : bool|array {
        $query = self::execute("SELECT user FROM note_shares WHERE note = :id", ["id" => $this->id]);
        $data = $query->fetchAll();
        $results = [];
        $isSharedWithEditPerrmission = false;
        foreach ($data as $row) {
            $results[] = User::getUserById($row["user"]);
        }
        // Fonction de comparaison pour trier les utilisateurs par nom
        $compareUsers = function ($a, $b) {
            return strcmp($a->getFullName(), $b->getFullName());
        };

        // Tri du tableau $results en utilisant la fonction de comparaison personnalisée
        usort($results, $compareUsers);
        return $results;
    }

    public function isSharedWithEditPerrmission($userId) : bool {
        $query = self::execute("SELECT editor FROM note_shares WHERE note = :id AND user = :userId", ["id" => $this->id, "userId" => $userId]);
        $data = $query->fetch();
    
        if (is_array($data) && array_key_exists("editor", $data)) {
            return (bool)$data["editor"];
        }
        return false;
    }
    
    public function unarchive(){
        $noteId = $this->id;
        self::execute("UPDATE notes SET archived = 0 where id = :noteId",["noteId" => $noteId]);
    }
    public function archive(){
        $noteId = $this->id;
        self::execute("UPDATE notes SET archived = 1 where id = :noteId",["noteId" => $noteId]);
    }
    public function pin(){
        $noteId = $this->id;
        self::execute("UPDATE notes SET pinned = 1 where id = :noteId",["noteId" => $noteId]);
    }
    public function unPin(){
        $noteId = $this->id;
        self::execute("UPDATE notes SET pinned = 0 where id = :noteId",["noteId" => $noteId]);
    }
    abstract public function deleteNote() : void;

    public function updateWeight() {
        self::execute("UPDATE notes SET weight = :weight WHERE id = :id", ["id" => $this->id,"weight" =>$this->weight]);
    }

    public static function maxPinnedNotesWeight(int $ownerId) : int {
        $query = self::execute(
            "SELECT MAX(weight) as max_weight 
             FROM notes 
             WHERE owner = :ownerId AND pinned = 1 AND archived = 0",
            ["ownerId" => $ownerId]
        );
        $data = $query->fetch();
    
        return $data !== false ? (int)$data['max_weight'] : 0;
    }
    public static function maxOtherNotesWeight(int $ownerId) : int {
        $query = self::execute(
            "SELECT MAX(weight) as max_weight 
             FROM notes 
             WHERE owner = :ownerId AND pinned = 0 AND archived = 0",
            ["ownerId" => $ownerId]
        );
        $data = $query->fetch();
    
        return $data !== false ? (int)$data['max_weight'] : 0;
    }

}