<?php
namespace application\controllers;

use \application\controllers\base\Controller;
use \application\controllers\OrderController;
use \application\models\UserModel;

class UserController extends Controller {
    protected $order;

    public function __construct() {
        parent::__construct();
        $this->model = new UserModel($this->config->get('db'));
        $this->order = new OrderController();
    }

    public function before() {
        parent::before();

        if (!$this->isAuthenticated) {
            header('location: /authorisation');
        }
    }

    public function action_index() {
        try {
            $id = (int) $this->session->get('user_id');
            
            $user = $this->model->getUserById($id);

            if (is_null($user)) {
                throw new \Exception("User with id '$id' doesn't exist.");
            }

            $visited_pages = $this->session->get('visited_pages');

            $templateVars = array_merge($this->templateVars, ['user' => $user], ['visited_pages' => $visited_pages]);

            return $this->view->render('user/index', $templateVars);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->view::getInstance()->render('error', ['message' => 'Error 500']);
        }
    }

    public function action_signout() {
        $id = (int) $this->session->get('user_id');
        
        $this->model->signout($id);
        
        $this->response->unsetCookie('user_id');
        $this->response->unsetCookie('session_key');
        
        session_destroy();
    }

    public function action_order() {
        $id = (int) $this->session->get('user_id');

        $templateVars = ['orders' => $this->order->getForUser($id)];

        return $this->view->render('user/orders', $templateVars);
    }

    public static function getHistory($pages = []) {
        $page_name = strip_tags($_SERVER['REQUEST_URI']);
        
        $page = ($page_name == '/') ? '/main' : $page_name;
        
        if (count($pages) > 0 && $pages[count($pages) - 1] == $page) {
            array_pop($pages);
        }
        
        if (count($pages) == 5) {
            array_shift($pages);
        }
        $pages[] = $page;

        return $pages;
    }
}
