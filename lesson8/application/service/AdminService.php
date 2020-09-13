<?php
namespace application\service;

use \application\core\BaseService;

class AdminService extends BaseService {
    /**
     * Проверяет, существует ли сессия администратора
     * 
     * @return bool
     */
    public function isAdminSessionExist() {
        $route = $this->router->getRoute();

        if (!$this->validator->boolFilter($this->session->get('admin')) && 
            $route['action'] == 'index') {
            return false;
        }
        return true;
    }

    /**
     * Возвращает пароль администратора из массива $_POST
     * 
     * @return string
     */
    public function getAdminPassword() {
        return $this->validator->stringFilter($this->request->post('password'));
    }

    /**
     * Создает сессию администратора
     * 
     * @return void
     */
    public function setAdminSession($rights) {
        $this->session->set('admin', true);
        $this->session->set('rights', $rights);
    }

    /**
     * Возвращает из массива $_SESSION идентификатор прав администратора
     * 
     * @return integer
     */
    public function getAdminRights() {
        if ($this->isAdminSessionExist()) {
            return $this->validator->intFilter($this->session->get('rights'));
        }
    }

    /**
     * Возвращает массив с изменнеым статусом заказа из массива $_POST
     * 
     * @return array
     */
    public function getNewStatus() {
        return $this->validator->arrayFilter($this->request->post(), [
            'id' => FILTER_SANITIZE_NUMBER_INT,
            'status' => FILTER_SANITIZE_NUMBER_INT
        ]);
    }
}
    