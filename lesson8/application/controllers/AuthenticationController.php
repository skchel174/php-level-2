<?php
namespace application\controllers;

use \application\core\BaseController;
use \application\service\UserService;
use \application\models\UserModel;
use \application\models\GlobalModel;

class AuthenticationController extends BaseController {
    protected $service,
              $userModel,
              $globalModel;

    public function __construct() {
        parent::__construct();
        $this->service = new UserService();
        $this->userModel = new UserModel('gallery');
        $this->globalModel = new GlobalModel($this->config->globalElements());
    }

    /**
     * Создает пользовательскую сессию по идентификатору, сохраненному в куки.
     * Если идентификатор сессии в куках не соответствует идентификатору в БД, удаляет сессионные куки.
     * 
     * @return string|bool - имя пользователя, либо null в случае отсутствия авторизации
     */
    public function authentication() {
        if ($this->service->isUserSessionExist()) {
            return $this->service->getUserName();
        }

        $userCookie = $this->service->getUserCookie();

        $result = $this->userModel->checkUserSession($userCookie);
        
        if (!$result) {
            $this->service->unsetUserCookie();
            return null;
        }

        $this->service->setUserSession($result['id'], $result['name']);
        return $this->service->getUserName();
    }

    /**
     * передает view в глобальные элементы сайта (title, menu...)
     * 
     * @return void
     */
    public function setGlobalElements($user) {
        $elements = $this->globalModel->getElements($user);

        $this->view->addGlobal('global', $elements);
    }

    public function checkUserSession() {
        $user_id = $this->service->getUserId();

        if (empty($user_id)) {
            return false;
        }

        return $user_id;
    }

    public function visitedPagesLogger($user_id) {
        $pages = [];

        if (empty($this->service->getVisitedPages())) {
            $pages = $this->userModel->getVisitedPages($user_id);
        }
        
        $page = $this->service->runPageLogger($pages);

        if (!is_null($page)) {         
            $this->userModel->setVisitedPage($user_id, $page);
        }
    }
}
