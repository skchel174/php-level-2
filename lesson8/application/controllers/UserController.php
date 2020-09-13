<?php
namespace application\controllers;

use \application\core\BaseController;
use \application\service\UserService;
use \application\models\UserModel;
use \application\models\OrderModel;
use \application\components\Helper;

class UserController extends BaseController {
    protected $service,
              $userModel,
              $orderModel;

    public function __construct() {
        parent::__construct();
        $this->service = new UserService();
        $this->userModel = new UserModel('gallery');
        $this->orderModel = new OrderModel('gallery', $this->config->orderStatus());
        $this->user_id = $this->service->getUserId();
    }

    public function before() {
        parent::before();

        $helper = new Helper($this);

        $helper->next('entry')
               ->next('checkRules');
    }

    /**
     * В зависимости от наличия или отсутствия авторизации,
     * возвращает заголовок с соответствующим адресом.
     * 
     * @return void
     */
    public function entry() {
        $result = $this->service->checkEntryPoint($this->user_id);

        if ($result !== true) {
            $this->response->setHeader('Location: /user/' . $result);
        }
    }

    /**
     * Проверяет у пользователя прав администратора,
     * при их наличии создает ссылку в профиле пользователя на страницу администратора
     * 
     * @return void
     */
    public function checkRules() {
        $rules = $this->userModel->getUserRules($this->user_id);

        if ($rules > 0) {
            $this->view->addGlobal('profile', ['admin' => 'admin']);
        }
    }

    /**
     * Генерирует и возвращает пользователю разметку страницы авторизации.
     * 
     * @return void
     */
    public function action_authorization() {
        $data = $this->view->render('user/authorization');

        $this->response->html($data);
    }

    /** 
     * Генерирует и возвращает пользователю разметку страницы профиля.
     * 
     * @return void
     */
    public function action_profile() {
        $data = $this->view->render('user/profile', ['user' => $this->userModel->getProfile($this->user_id),
                                                    'visited_pages' => $this->service->getVisitedPages()]);
                                                    
        $this->response->html($data);
    }

    /**
     * Генерирует и возвращает пользователю разметку блока заказов.
     * 
     * @return void
     */
    public function action_orders() {
        $data = $this->view->render('user/orders', ['orders' => $this->orderModel->getUserOrders($this->user_id)]);

        $this->response->html($data);
    }

    /**
     * Авторизует существующего пользователя.
     * Если данные формы не пришли, выбрасывает исключение с обшибкой.
     * Если данные формы не прошли валидацию, возвращает пользователю сообщение об этом.
     */
    public function action_signin()  {
        $request = $this->service->getAuthorisationForm('signin');
 
        if (!$request) {
            throw new \Exception('400');
        }

        $result = $this->userModel->signin($request);

        if (!is_array($result)) {
            $html = $this->view->render('user/authorization', ['error' => $result]);
            return $this->response->html($html);
        }

        $this->service->authorizeUser($result);

        $this->response->setHeader('Location: /user/profile');
    }

    /**
     * Авторизует несуществующего пользователя.
     * Если данные формы не пришли, выбрасывает исключение с обшибкой.
     * Если данные формы не прошли валидацию, возвращает пользователю сообщение об этом.
     */
    public function action_signup() {
        $request = $this->service->getAuthorisationForm('signup');

        if (!$request) {
            throw new \Exception('400');
        }

        $result = $this->userModel->signup($request);

        if (!is_array($result)) {
            $html = $this->view->render('user/authorization', ['error' => $result]);
            return $this->response->html($html);
        }

        $this->service->authorizeUser($result);

        $this->response->setHeader('Location: /user/profile');
    }

    /**
     * Удаляет пользовательскю сессию и сессионные куки (при наличии).
     * 
     * @return void
     */
    public function action_signout() {
        $this->userModel->signout($this->user_id);

        $this->service->unsetUserSession();
        $this->service->unsetUserCookie();

        $this->response->setHeader('Location: /user/authorization');
    }
}
