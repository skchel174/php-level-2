<?php
namespace application\service;

use \application\core\BaseService;

class UserService extends BaseService {
    /**
     * 
     */
    public function getUserName() {
        return $this->validator->stringFilter($this->session->get('user_name'));
    }

    /**
     * Проверяет наличие авторизации при переходе на соответствующие страницы.
     * 
     * @return string|bool -  имя страницы, либо true в случае успеха.
     */
    public function checkEntryPoint() {
        $route = $this->router->getRoute();

        if ($route['action'] == 'authorization' && $this->isUserSessionExist()) {
            return 'profile';
        }

        if ($route['action'] == 'profile' && !$this->isUserSessionExist()) {
            return 'authorization';
        }

        return true;
    }

    /**
     * Создает пользовательскую сессию,
     * при включенном checkbox remember - создает куки с идентификатором пользовательской сессии.
     * 
     * @param array $user - массив с аднными пользователя.
     * @return void
     */
    public function authorizeUser($user) {
        $this->setUserSession($user['id'], $user['name']);
        if ($user['remember']) {
            $this->cookie->set('user_id', $user['id'], time() + 3600);
            $this->cookie->set('session_key', $user['session_key'], time() + 3600);
        }
    }

    /**
     * Создает пользовательскую сессию,
     * 
     * @param string $user_id - идентификатор пользователя
     * @param string @user_name - иям пользователя
     * @return void
     */
    public function setUserSession($user_id, $user_name) {
        $this->session->set('user_id', $user_id);
        $this->session->set('user_name', $user_name);
    }

    /**
     * Удалает пользователбскую сессию
     * 
     * @return void
     */
    public function unsetUserSession() {
        $this->session->destroy();
    }
 
    /**
     * Првоеряет наличие куки с идентификатором пользовательской сессии
     * 
     * @return bool
     */
    public function isUserCookieExist() {
        if (!empty($this->validator->intFilter($this->cookie->get('user_id'))) && 
            !empty($this->validator->stringFilter($this->cookie->get('session_key')))) {
            return true;
        }
        return false;
    }

    /**
     * Удалает куки с идентификатором пользовательской сессии
     * 
     * @return void
     */
    public function unsetUserCookie() {
        $this->cookie->unset('user_id');
        $this->cookie->unset('session_key');
    }

    /**
     * Возвращает куки с идентификатором пользовательской сессии
     * 
     * @return array|null
     */
    public function getUserCookie() {
        if (!$this->isUserCookieExist()) {
            return null;
        }

        return [
                'user_id' => $this->validator->intFilter($this->cookie->get('user_id')),
                'session_key' => $this->validator->stringFilter($this->cookie->get('session_key'))
               ];
    }

    /**
     * Валидирует данные формы авторизации
     * 
     * @return array
     */
    public function getAuthorisationForm($action) {
        return $this->validator->arrayFilter($this->request->post(), $this->requestDefinitions($action));
    }

    /**
     * Возвращает массив с параметрами валидации и фильтрации
     * 
     * @return array
     */
    protected function requestDefinitions($action) {
        $definitions = [
            'signin' => [
                'login' => FILTER_SANITIZE_STRING,
                'password' => FILTER_SANITIZE_STRING,
                'remember' => FILTER_SANITIZE_STRING,
            ],
            'signup' => [
                'login' => FILTER_SANITIZE_STRING,
                'password' => FILTER_SANITIZE_STRING,
                'repeated' => FILTER_SANITIZE_STRING,
                'remember' => FILTER_SANITIZE_STRING,
                'name' => FILTER_SANITIZE_STRING,
                'email' => [
                    'filter' => FILTER_VALIDATE_REGEXP,
                    'options' => ['regexp' => '/^.+@.+\..+$/'],
                ]
            ],
        ];

        return $definitions[$action];
    }

    /**
     * Принимет массив с адресами страниц, полученных из БД, если он не пустой сохраняет его в сессю.
     * Получает адрес текущей страницы, сохраняет его в сессию и возвращает в контроллер для записи в БД.
     * 
     * @param array
     * @return string
     */
    public function runPageLogger($pages) {
        if (!empty($pages)) {
            $this->setVisitedPages($pages);
        }

        $pages = $this->getVisitedPages() ?? [];

        $page = $this->validator->stringFilter($this->server->get('REQUEST_URI'));

        $page = ($page == '/') ? '/main' : $page;
        
        if ($this->validator->stringFilter($this->request->get('request')) == 'ajax') {
            return null;
        }
        
        if (in_array($page, $pages)) {
            return null;
        }
        
        if (count($pages) == 5) {
            array_shift($pages);
        }

        $pages[] = $page;

        $this->setVisitedPages($pages);

        return $page;
    }

    /**
     * Получает из сессии адреса посещенных пользователем страниц 
     * 
     * @return array
     */
    public function getVisitedPages() {
        return $this->session->get('visited_pages');
    }

    /**
     * Перезаписывает в сессии массив с адресами посещенных пользователем страниц
     * 
     * @param array $pages - массив с адресами старниц
     * @return void
     */
    public function setVisitedPages($pages) {
        $this->session->set('visited_pages', $pages);
    }
}
    