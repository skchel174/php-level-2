<?php
namespace application\controllers;

use \application\core\BaseController;
use \application\service\AdminService;
use \application\models\AdminModel;
use \application\models\OrderModel;
use \application\components\Helper;

class AdminController extends BaseController {
    protected $service,
              $userModel,
              $orderModel;

    public function __construct() {
        parent::__construct();
        $this->service = new AdminService();
        $this->adminModel = new AdminModel('gallery');
        $this->orderModel = new OrderModel('gallery', $this->config->orderStatus());
        $this->user_id = $this->service->getUserId();
    }

    public function before() {
        parent::before();

        $helper = new Helper($this);

        $helper->next('entry')
               ->next('checkRights');
    }

    /**
     * В случае отсутствия сессии администратора перенаправляет на старницу администратора,
     * возвращает false для прерывания зепочки $helper
     * 
     * @return bool
     */
    public function entry() {
        if (!$this->service->isAdminSessionExist()) {
            $this->response->setHeader('Location: /admin/authorization');
            return false;
        }
    }

    /**
     * Возвращает идентификатор прав администратора
     * 
     * @return integer
     */
    public function checkRights() {
        return $this->service->getAdminRights();
    }

    /**
     * Возвращает разметку страницы авторизации администратора
     * 
     * @return void
     */
    public function action_authorization() {
        $data = $this->view->render('admin/authorization');

        $this->response->html($data);
    }

    /**
     * Авторизует пользователя как администратора,
     * в случае успеха перенаправляет на страницу администратора,
     * в случае неудачи - возвращает ответ с текстом ошибки
     */
    public function action_signin() {
        $password = $this->service->getAdminPassword();

        $result = $this->adminModel->signin($this->user_id, $password);

        if (!is_array($result)) {
            $html = $this->view->render('admin/authorization', ['error' => $result]);
            return $this->response->html($html);
        }

        $this->service->setAdminSession($result['rights']);
        $this->response->setHeader('Location: /admin');
    }

    /**
     * Возвращает разметку страницы администратора
     * 
     * @return void
     */
    public function action_index() {
        $data = $this->view->render('admin/index');

        $this->response->html($data);
    }

    /**
     * Генерирует и возвращает пользователю разметку блока заказов.
     * 
     * @return void
     */
    public function action_orders() {
        $data = $this->view->render('admin/orders', ['orders' => $this->orderModel->getAllOrders()]);

        $this->response->html($data);
    }

    /**
     * Получает измененный статус заказа, передает информацию о нем в модель.
     * Возвращает пользователю с результатом операции.
     * 
     * @return void
     */
    public function action_status() {
        $status = $this->service->getNewStatus();
        $result = $this->orderModel->setOrderStatus($status);

        $this->response->setCode($result);
    }
}
