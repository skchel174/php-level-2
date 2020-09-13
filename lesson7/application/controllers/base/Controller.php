<?php
namespace application\controllers\base;

use \application\core\Application;
use \application\controllers\AuthenticationController;
use \application\views\View;

abstract class Controller {
    protected $session;
    protected $config;
    protected $router;
    protected $request;
    protected $response;
    protected $logger;
    protected $view;
    
    protected $model = null;
    protected $isAutentication;
    protected $templateVars = [];

    public function __construct() {
        $this->session = Application::getInstance()->session();
        $this->config = Application::getInstance()->config();
        $this->request = Application::getInstance()->request();
        $this->response = Application::getInstance()->response();
        $this->router = Application::getInstance()->router();
        $this->logger = Application::getInstance()->logger();
        $this->view = View::getInstance();
        $this->isAuthenticated = $this->authentication();
    }

    public function before() {
        $this->templateVars['title'] = $this->config->get('title');
    }

    private function authentication() {
        AuthenticationController::getInstance()->signinByCookie();

        if (!AuthenticationController::getInstance()->isSessionExist()) {
            return false;
        }

        AuthenticationController::getInstance()->setVisitedPages();

        $this->templateVars['account'] = $this->session->get('user_name');

        return true;
    }
}
