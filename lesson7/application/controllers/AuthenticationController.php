<?php
namespace application\controllers;

use \application\models\UserModel;
use \application\core\Application;
use \application\controllers\UserController;
use \application\traits\Singleton;

class AuthenticationController {
    use Singleton;

    private $session;
    private $config;
    private $request;
    private $response;
    private $model;

    public function __construct() {
        $this->session = Application::getInstance()->session();
        $this->config = Application::getInstance()->config();
        $this->request = Application::getInstance()->request();
        $this->response = Application::getInstance()->response();
        $this->model = new UserModel($this->config->get('db'));
    }

    public function signinByCookie() {
        $user_id = strip_tags($this->request->cookie('user_id'));
        $session_key = strip_tags($this->request->cookie('session_key'));
        
        if (empty($user_id) && empty($session_key)) {
            return false;
        }
        
        $result = $this->model->checkUserSession($user_id, $session_key);
        
        if (!$result) {
            $this->model->unsetUserSession($user_id, $session_key);
            $this->resposne->unsetCookie('user_id');
            $this->response->unsetCookie('session_key');
            return false;
        }

        $this->session->set('user_id', $user_id);
        $this->session->set('user_name', $result['name']);
        return true;
    }

    public function isSessionExist() {
		if ($this->session->get('user_id') && $this->session->get('user_name')) {
			return true;
		}
		return false;
    }

    public function getUserId() {
        $user_id = (int) $this->session->get('user_id');
        $product_id = (int) $this->request->post('product');
    
        if ($user_id == 0) {
            $user_id = strip_tags(htmlspecialchars($this->request->cookie('unreg_user_id')));
        }

        return !empty($user_id) ? $user_id : null;
    }

    public function setUnregisteredUserId() {
        $user_id = md5(random_bytes(16));
        $this->response->setCookie('unreg_user_id', $user_id, time() + 3600 * 24 * 365);

        return $user_id;
    }
    
    public function setVisitedPages() {
        $user_id = (int) $this->session->get('user_id');

        if (is_null($this->session->get('visited_pages'))) {
            $pages = $this->model->getVisitedPages($user_id) ?? [];
            $this->session->set('visited_pages', $pages);
        }

        $pages = UserController::getHistory($this->session->get('visited_pages'));  
        
        $this->model->setVisitedPage($user_id, $pages[count($pages) - 1]);
        $this->session->set('visited_pages', $pages);
    }
}
