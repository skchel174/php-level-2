<?php
namespace application\controllers;

use \application\controllers\base\Controller;
use \application\models\UserModel;

class AuthorisationController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new UserModel($this->config->get('db'));
    }

    public function before() {
        parent::before();

        if ($this->isAuthenticated) {
            header('location: /user');
        }
    }

    public function action_index() {
        return $this->view->render('authorisation/index', $this->templateVars);
    }

    public function action_signin() {
        $login = trim(strip_tags(htmlspecialchars($_POST['login'])));
        $password = trim(strip_tags(htmlspecialchars($_POST['password'])));
        $remember_user = (bool) $_POST['remember'];

        if (empty($login)) {
            return $this->returnAuthError('Login not entered!');
        }

        if (empty($password)) {
            return $this->returnAuthError('Password not entered!');
        }
    
        $user = $this->model->getUserByLogin($login);

        if (!$user) {
            return $this->returnAuthError("User with login '$login' doesn't exist!");
        }

        $hash_password = md5($password . $user['salt']);
    
        if ($hash_password !== $user['password']) {
            return $this->returnAuthError('Wrong password!');
        }

        $this->setAuthSession($user['id'], $user['name']);

        if ($remember_user) {
            $session_key = $this->model->getUserSession($user['id']);
            if (!$session_key) {
                $session_key = $this->model->setUserSession($user['id']);                
            }
            $this->setAuthCookie($user['id'], $session_key);
        }

        header('location: /user');
    }

    public function action_signup() {
        $login = trim(strip_tags(htmlspecialchars($_POST['login'])));
        $password = trim(strip_tags(htmlspecialchars($_POST['password'])));
        $repeated_password = trim(htmlspecialchars(strip_tags($_POST['repeated'])));
        $name = trim(strip_tags(htmlspecialchars($_POST['name'])));
        $email = trim(strip_tags(htmlspecialchars($_POST['email'])));
        $remember_user = (bool) $_POST['remember'];

        if (empty($login)) {
            return $this->returnAuthError('Login not entered!');
        }
        
        if (empty($password)) {
            return $this->returnAuthError('Password not entered!');
        }

        if (empty($repeated_password)) {
            return $this->returnAuthError('Repeat password!');
        }

        if ($password != $repeated_password) {
            return $this->returnAuthError('Password confirmation error!');
        }        

        if (empty($name)) {
            return $this->returnAuthError('Name not entered!');
        }

        if (empty($email)) {
            return $this->returnAuthError('Email not entered!');
        } elseif (!preg_match("/^.+@.+\..+$/", $email)) {
            return $this->returnAuthError('Email is not valid!');
        }
        
        if ($this->model->checkLogin($login)) {
            return $this->returnAuthError('Login is already in use!');
        }

        if (!$id = $this->model->signup($name, $login, $password, $email)) {
            return $this->returnAuthError('Registration error!');
        }
        
        $this->setAuthSession($id, $name);
        
        if ($remember_user) {
            $session_key = $this->model->setUserSession($id);                
            $this->setAuthCookie($id, $session_key);
        }

        header('location: /user');
    }

    protected function setAuthSession($id, $name) {
        $this->session->set('user_id', $id);
        $this->session->set('user_name', $name);
    }

    protected function setAuthCookie($id, $session_key) {
        setcookie('user_id', $id, time() + 3600 * 24 * 365, '/');
        setcookie('session_key', $session_key, time() + 3600 * 24 * 365, '/');
    }

    protected function returnAuthError($error) {
        $this->view->render('authorisation/index', array_merge($this->templateVars, ['error' => $error]));    
    }
}
