<?php
namespace application\core;

use \application\core\Application;
use \application\core\View;
use \application\http\Response;
use \application\components\Helper;
use \application\controllers\AuthenticationController;

abstract class BaseController {
    protected $config,
              $logger,
              $view,
              $response;

    public function __construct() {
        $this->config = Application::getInstance()->config();
        $this->logger = Application::getInstance()->logger();
        $this->view = new View();
        $this->response = new Response();
    }

    /**
     * Аутентификация пользователя,
     * формирвоание глобального контента, с учетом результатов аутентификации.
     */
    public function before() {
        $authController = new AuthenticationController();
        $helper = new Helper($authController);

        $helper->next('authentication')
               ->next('setGlobalElements')
               ->next('checkUserSession')
               ->next('visitedPagesLogger');
    }          
}
