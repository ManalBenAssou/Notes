<?php

require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerMain extends Controller {

    //si l'utilisateur est connecté, redirige vers sa liste de notes.
    //sinon, produit la vue d'accueil.
    public function index() : void {
        if ($this->user_logged()) {
            $this->redirect("user", "notes");
        } else {
            (new View("login"))->show();
        }
        
    }

    //gestion de la connexion d'un utilisateur
    public function login() : void {
        $mail = '';
        $password = '';
        $errors = [];
        if (isset($_POST['mail']) && isset($_POST['password'])) { //note : pourraient contenir des chaînes vides
            $mail = $_POST['mail'];
            $password = $_POST['password'];

            $errors = User::validate_login($mail, $password);
            if (empty($errors)) {
                $this->log_user(User::get_user_by_mail($mail));
            }
        }
        (new View("login"))->show(["mail" => $mail, "password" => $password, "errors" => $errors]);
    }

    public function logout(): void
    {
        $_SESSION = array();
        session_destroy();
        $this->redirect();
    }
        
    //gestion de l'inscription d'un utilisateur
    public function signup() : void {
        $full_name = '';
        $password = '';
        $mail = '';
        $password_confirm = '';
        $errors = [];

        if (isset($_POST['full_name']) && isset($_POST['password']) && isset($_POST['password_confirm']) && isset($_POST['mail'])) {
            $full_name = trim($_POST['full_name']);
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];
            $mail = $_POST['mail'];
            $user = new User($mail, Tools::my_hash($password),$full_name,Role::USER,0);
            $errors = User::validate_unicity($mail);
            $errors = array_merge($errors, $user->validate_fullName($full_name));
            $errors = array_merge($errors, User::validate_passwords($password, $password_confirm));
            $errors = array_merge($errors, User::validate_email($mail));

            if (empty($errors)) { 
                $user->persist(); //sauve l'utilisateur
                $this->log_user($user);
            }
        }
        (new View("signup"))->show(["mail" => $mail,"full_name" => $full_name, "password" => $password, 
                                         "password_confirm" => $password_confirm, "errors" => $errors]);
    }


}
