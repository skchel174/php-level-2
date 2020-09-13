<?php
namespace application\controllers;

use \application\core\BaseController;
use \application\models\CartModel;
use \application\service\ProductService;

class CartController extends BaseController {
    public function __construct() {
        parent::__construct();

        $this->cartModel = new CartModel('gallery');
        $this->service = new ProductService();

        $this->product_id = $this->service->getProductId();
        $this->user_id = $this->service->getUserCartId();
    }

    /**
     * Получает массив с товарами в корзине и передает их пользователю
     * 
     * @return void
     */
    public function action_index() {
        $data = $this->view->render('cart/index', ['products' => $this->cartModel->getProducts($this->user_id)]);

        $this->response->html($data);
    }

    /**
     * Увеличивает количество товара в корзине, возвращает код с результатом операции
     * 
     * @return void
     */
    public function action_increase() {         
        $data = $this->cartModel->increaseProduct($this->user_id, $this->product_id);

        $this->response->setCode($data);
    }

    /**
     * Уменьшает количество товара в корзине, возвращает код с результатом операции
     * 
     * @return void
     */
    public function action_decrease() {
        $data = $this->cartModel->decreaseProduct($this->user_id, $this->product_id);

        $this->response->setCode($data);
    }

    /**
     * Удаляет товар из корзины, возвращает код с результатом операции
     * 
     * @return void
     */    
    public function action_remove() {
        $data = $this->cartModel->removeProduct($this->user_id, $this->product_id);

        $this->response->setCode($data);
    }

    /**
     * Создает заказ, возвращает пользователю сообщение с результатом операции
     * 
     * @return void
     */
    public function action_order() {
        $data = $this->cartModel->makeOrder($this->service->getUserId());

        $this->response->json($data);
    }
}
