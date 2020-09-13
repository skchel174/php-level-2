<?php
namespace application\core;

use \application\core\Application;
use \application\components\Validator;

abstract class BaseService {
    protected $route,
              $request,
              $server,
              $session,
              $cookie,
              $validaor;

    public function __construct() {
        $this->router = Application::getInstance()->router();
        $this->request = Application::getInstance()->request();
        $this->server = Application::getInstance()->server();
        $this->session = Application::getInstance()->session();
        $this->cookie = Application::getInstance()->cookie();
        $this->validator = new Validator();
    }

    /**
     * Возвращает идентификатор пользователя.
     * 
     * @return string|null
     */    
    public function getUserId() {
        if ($this->isUserSessionExist()) {
            return $this->validator->intFilter($this->session->get('user_id'));
        }

        return null;
    }

    /**
     * Проверяет наличие пользовательской сессии.
     * 
     * @return bool
     */
    public function isUserSessionExist() {
        if ($this->validator->intFilter($this->session->get('user_id')) && 
            $this->validator->stringFilter($this->session->get('user_name'))) {
			return true;
        }
        
		return false;
    }
}    
