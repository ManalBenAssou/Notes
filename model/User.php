<?php

require_once "framework/Model.php";
class Role{
    const ADMIN = 'admin';
    const USER = 'user';
}

class User extends Model {

    private String $password;
    private string $hashed_password;
    private string $mail ;
    private string $full_name;
    private string $role;
    private int $id = 0;

    public function __construct( string $mail,
      string $hashed_password, 
      string $full_name,
       string $role, int $id =0) {
        $this-> hashed_password = $hashed_password;
        $this->mail = $mail;
        $this->full_name = $full_name;
        $this->role = $role;
        $this->id =$id; 
    }
    public function getFullName(){
        return $this -> full_name;
    }
    public function getMail(){
        return $this -> mail;
    }
    public function setMail($mail){
        $this->mail = $mail;
    }

    public function getId() : int {
        return $this->get_user_idFromDatabase();
    }
    public function getPassword() : string {
        return $this->hashed_password;
    }

    public function get_user_idFromDatabase() : int|false {
        $query = self::execute("SELECT id FROM users WHERE mail = :mail", ["mail" => $this->mail]);
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return 0;
        } else {
            return $data['id'];
        }
    }
    

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getNotesSharedWithMeHaveLabel() : ?array {
        $userId = $this->getId();
        
        // Utilisation de la requête SQL JOIN pour obtenir les IDs des notes partagées
        $query = self::execute("SELECT note_labels.note 
                                FROM note_shares, note_labels                      
                                WHERE note_shares.user = :userId AND note_labels.note = note_shares.note", 
                                ["userId" => $userId]);
        
        if ($query->rowCount() == 0) {
            return null;
        } else {
            $data = $query->fetchAll(PDO::FETCH_COLUMN);
            $notes = [];
    
            foreach ($data as $noteId) {
                $notes[] = Note::getNoteById($noteId);
            }
    
            return $notes;
        }
    }

    public function getNotesWithLabel() : array {
        $query = self::execute("SELECT DISTINCT note FROM note_labels, notes WHERE note_labels.note = notes.id AND notes.owner = :userId"
        , ["userId" => $this->getId()]);
        $data = $query->fetchAll();
        $notes = [];

        foreach($data as $row) {
            $notes[] = Note::getNoteById($row['note']); 
        }
        
        return $notes;
    }

    public function getMyNotesByLabel(string $label) : array {
        $query = self::execute("SELECT * FROM note_labels nl, notes n WHERE nl.note = n.id AND n.owner = :userId AND nl.label = :label", 
        ["label" => $label, "userId" => $this->getId()]);
        $data = $query->fetchAll();
        $notes = [];

        $note = null;
        foreach ($data as $row) {
            $note = Note::getNoteById($row['note']);
            
            $notes[] = $note;
        }

        return $notes;
    }

    public function getSharedNotesByLabel(string $label) : array {
        $query = self::execute("SELECT nl.note FROM note_labels nl, note_shares ns  WHERE nl.note = ns.note AND ns.user = :userId AND nl.label = :label", 
        ["label" => $label, "userId"=>$this->getId()]);
        $data = $query->fetchAll();
        $notes = [];

        $note = null;
        foreach ($data as $row) {
            $note = Note::getNoteById($row['note']);
            
            $notes[] = $note;
        }

        return $notes;
    }
    public function get_notes() : array {
        return Note::getNotesByUser($this->getId());
    }

    public function getChecklistNotes() : array {
        return Note::getChecklistNotesByUser($this->getId());
    }
    public function save($newPassword) {
        $newHashedPassword = Tools::my_hash($newPassword);
        self::get_user_by_mail($this->mail);
            self::execute("UPDATE users SET hashed_password=:newHashedPassword WHERE mail=:mail ", 
                          ["mail"=>$this->mail, "newHashedPassword"=>$newHashedPassword]);
    }
    /*public function save_profile($newEmail, $newFullName) {
        self::get_user_by_mail($this->mail);
        self::execute("UPDATE users SET mail=:newEmail, full_name=:newFullName WHERE mail=:mail", 
                      ["mail" => $this->mail, "newEmail" => $newEmail, "newFullName" => $newFullName]);
    }*/
    public function save_profile($newEmail, $newFullName) {
        // Récupération de l'utilisateur actuel basé sur l'e-mail avant la mise à jour
        // pour s'assurer que nous mettons à jour le bon utilisateur.
        $currentUser = self::get_user_by_mail($this->mail);
    
        // Mettre à jour seulement si l'utilisateur actuel est trouvé
        if ($currentUser) {
            self::execute("UPDATE users SET mail=:newEmail, full_name=:newFullName WHERE mail=:mail", 
                          ["mail" => $this->mail, "newEmail" => $newEmail, "newFullName" => $newFullName]);
            
            // Mettre à jour l'e-mail dans l'objet utilisateur courant après la mise à jour réussie de la base de données.
            $this->mail = $newEmail;
            $this->full_name = $newFullName;
        }
    }
    
    /*
    $existingUser = self::get_user_by_mail($this->mail);
if ($existingUser) {
$sql = "UPDATE Users SET mail = :mail, hashed_password = :password, full_name = :full_name, role = :role WHERE id = :user_id";
$params = [
"mail" => $this->mail,
"password" => $this->password,
"full_name" => $this->full_name,
"role" => $this->role,
"user_id" => $existingUser->id
];
self::execute($sql, $params);


    
    */

    public function persist() : User {

        if(self::get_user_by_mail($this->mail))
            self::execute("UPDATE Users SET mail = :mail, hashed_password = :hashed_password, full_name = :full_name, role =:role WHERE id = :user_id",
                          ["user_id"=>$this->id,
                          "mail" => $this->mail,
                          "hashed_password" => $this->hashed_password,
                          "full_name" => $this->full_name,
                          "role" => $this->role,
                        ]);
        else
            self::execute("INSERT INTO users(mail,hashed_password,full_name,role) VALUES(:mail,:hashed_password,:full_name,:role)", 
                          ["mail"=>$this->mail,
                           "hashed_password"=>$this->hashed_password,
                            "full_name"=> $this->full_name,
                            "role"=> $this->role

                        ]);
                        $this->id = self::lastInsertId();
        return $this;
    }

    public static function get_user_by_mail(string $mail) : User|false {
        $query = self::execute("SELECT * FROM users where mail = :mail", ["mail"=>$mail]);
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["mail"], $data["hashed_password"], $data["full_name"], $data["role"]);
        }
    }
    
    public function setUsername($newUsername) {
        $this->full_name= $newUsername;
    }        
    public static function get_users() : array {
        $query = self::execute("SELECT * FROM users order by full_name", []);
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {

            $results[] = new User($row["mail"], $row["hashed_password"], $row["full_name"], $row["role"]);
        }
        return $results;
    }

    public function getNotesSharedAsEditor($sharedUserId) : ?array {
        $userId = $this->getId();
        
        $query = self::execute("SELECT notes.id FROM notes 
                                INNER JOIN note_shares ON notes.id = note_shares.note 
                                WHERE note_shares.user = :userId AND note_shares.editor = 1
                                AND notes.owner = :sharedUserId", ["userId" => $userId, "sharedUserId"=>$sharedUserId]);
        
        if ($query->rowCount() == 0) {
            return null;
        } else {
            $data = $query->fetchAll(PDO::FETCH_COLUMN);
            $notes = [];
    
            foreach ($data as $noteId) {
                $notes[] = Note::getNoteById($noteId);
            }
    
            return $notes;
        }
    }

    public function getNotesSharedAsReader($sharedUserId) : ?array {
        $userId = $this->getId();
        
        $query = self::execute("SELECT notes.id FROM notes 
                                INNER JOIN note_shares ON notes.id = note_shares.note 
                                WHERE note_shares.user = :userId AND note_shares.editor = 0
                                AND notes.owner = :sharedUserId", ["userId" => $userId, "sharedUserId"=>$sharedUserId]);
        
        if ($query->rowCount() == 0) {
            return null;
        } else {
            $data = $query->fetchAll(PDO::FETCH_COLUMN);
            $notes = [];
    
            foreach ($data as $noteId) {
                $notes[] = Note::getNoteById($noteId);
            }
    
            return $notes;
        }
    }
    

   
    public static function check_password(string $clear_password, string $hash) : bool {
        return $hash === Tools::my_hash($clear_password);
    }

    public static function validate_login(string $mail, string $password) : array {
        $errors = [];
        $user = User::get_user_by_mail($mail);
        if ($user) {
            if (!self::check_password($password, $user->hashed_password)) {
                $errors[] = "Wrong password. Please try again.";
            }
        } else {
            $errors[] = "Can't find a user with the mail '$mail'. Please sign up.";
        }
        return $errors;
    }



    public static function validate_password(string $password) : array {
        $errors = [];
        if (strlen($password) < 8 || strlen($password) > 16) {
            $errors[] = "Password length must be between 8 and 16.";
        } if (!((preg_match("/[A-Z]/", $password)) && preg_match("/\d/", $password) && preg_match("/['\";:,.\/?!\\-]/", $password))) {
            $errors[] = "Password must contain one uppercase letter, one number and one punctuation mark.";
        }
        return $errors;
    }
    public static function validate_unicity($mail) : array {
        $errors = [];
        $user = self::get_user_by_mail($mail);
        if ($user) {
            $errors[] = "This user already exists.";
        } 
        return $errors;
    }
    public static function validate_passwords(string $password, string $password_confirm) : array {
        $errors = [];
        if($password== null){
            $errors[] = "You have to enter a password.";
        }
        if ($password !== $password_confirm) {
            $errors[] = "You have to enter twice the same password.";
        }
        return $errors;
    }
    

    /*public function validate_mail(): array {
        $errors = [];    
        if (empty($mail)) {
            $errors[] = "Email is required.";
        }
        if (!(preg_match("/^[a-zA-Z0-9]+@[a-zA-Z]+\.[a-zA-Z]$/", $this->mail))) {
            $errors[] = "Invalid email format.";
        }
    
        return $errors;
    }*/
    public static function validate_email($email): array {
        $errors = [];    
        if (empty($email) || $email==null) {
            $errors[] = "Email is required.";
        }
        if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email)) {
            $errors[] = "Invalid email format.";
        }
    
        return $errors;
    }

    public static function validate_fullName($full_name) : array {
        $errors = [];
        if (!strlen($full_name) > 0) {
            $errors[] = "username is required.";
        } if (!(strlen($full_name) >= 3 && strlen($full_name) <= 16)) {
            $errors[] = "username length must be between 3 and 16.";
        } if (!(preg_match("/^[a-zA-Z][a-zA-Z0-9]*$/", $full_name))) {
            $errors[] = "username must start by a letter and must contain only letters and numbers.";
        }
        return $errors;
    }

    public static function getUserById($userId) : User|false {
        $query = self::execute("SELECT * FROM users where id = :userId", ["userId"=>$userId]);
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["mail"], $data["hashed_password"], $data["full_name"], $data["role"]);
        }
    }

    public function getNotesSharedWithMe() : ?array {
        $userId = $this->getId();
        
        // Utilisation de la requête SQL JOIN pour obtenir les IDs des notes partagées
        $query = self::execute("SELECT notes.id FROM notes 
                                INNER JOIN note_shares ON notes.id = note_shares.note 
                                WHERE note_shares.user = :userId", ["userId" => $userId]);
        
        if ($query->rowCount() == 0) {
            return null;
        } else {
            $data = $query->fetchAll(PDO::FETCH_COLUMN);
            $notes = [];
    
            foreach ($data as $noteId) {
                $notes[] = Note::getNoteById($noteId);
            }
    
            return $notes;
        }
    }
    
    public function getNotesSharedWithMeByUser(int $sharedUserId) : array {
        $userId = $this->getId();
        
        // Utilisation de la requête SQL JOIN pour obtenir les IDs des notes partagées
        $query = self::execute("SELECT notes.id FROM notes 
                                INNER JOIN note_shares ON notes.id = note_shares.note 
                                WHERE note_shares.user = :userId AND notes.owner = :sharedUserId", 
                                ["userId" => $userId, "sharedUserId" => $sharedUserId]);
        
        if ($query->rowCount() == 0) {
            return null;
        } else {
            $data = $query->fetchAll(PDO::FETCH_COLUMN);
            $notes = [];
    
            foreach ($data as $noteId) {
                $notes[] = Note::getNoteById($noteId);
            }
    
            return $notes;
        }
    }

    public function getUsersSharedWithMe() : ?array {
        $notes = $this->getNotesSharedWithMe();
    
        if ($notes == null) {
            return null;
        } else {
            $usersSet = [];  // Utilisation d'un tableau associatif pour éviter les doublons
            foreach ($notes as $note) {
                $ownerId = $note->getOwnerId();
                // Ajouter l'utilisateur au tableau associatif avec l'ID en tant que clé
                $usersSet[$ownerId] = User::getUserById($ownerId);
            }   
            // Trier le tableau par le nom des utilisateurs
            usort($usersSet, function ($a, $b) {
                return strcmp($a->full_name, $b->full_name);
            });
    
            // Convertir le tableau associatif en tableau numérique pour la sortie
            $users = array_values($usersSet);
    
            return $users;
        }
    }
    
    

}