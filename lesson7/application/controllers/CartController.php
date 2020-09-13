<?php
namespace application\controllers;

use \application\controllers\base\Controller;
use \application\controllers\AuthenticationController;
use \application\models\CartModel;

class CartController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new CartModel($this->config->get('db'));
    }

    public function action_index() {
        try {
            $user_id = AuthenticationController::getInstance()->getUserId();

            $products = $this->model->getProducts($user_id);
            
            if (is_null($products)) {
                throw new \Exception('Error while receiving cart.');
            }
            
            $vars = array_merge($this->templateVars, ['products' => $products]);

            return $this->view->render('cart/index', $vars);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->view->render('error', ['message' => 'Error 500']);
        }
    }

    public function action_increase() {
        try {
            $user_id = AuthenticationController::getInstance()->getUserId();            
            $product_id = (int) $this->request->post('product');

            $result = $this->model->increaseProduct($user_id, $product_id);

            if (!$result) {
                throw new \Exception('Product count increase error.');
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->view->render('error', ['message' => 'Error 500']);
        }
    }

    public function action_decrease() {
        try {
            $user_id = AuthenticationController::getInstance()->getUserId();            
            $product_id = (int) $this->request->post('product');

            $result = $this->model->decreaseProduct($user_id, $product_id);

            if (!$result) {
                throw new \Exception('Product count decrease error.');
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->view->render('error', ['message' => 'Error 500']);
        }
    }

    public function action_remove() {
        try {
            $user_id = AuthenticationController::getInstance()->getUserId();    
            $product_id = (int) $this->request->post('product');

            $result = $this->model->removeProduct($user_id, $product_id);

            if (!$result) {
                throw new \Exception('Product remove error.');
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->view->render('error', ['message' => 'Error 500']);
        }
    }

    public function action_order() {
        try {
            if (!AuthenticationController::getInstance()->isSessionExist()) {
                return $this->response->setResponse([
                    'result' => 'fail',
                    'message' => 'To make an order, you must register!'
                ]);
            }

            $user_id = AuthenticationController::getInstance()->getUserId(); 

            $result = $this->model->makeOrder($user_id);

            if ($result) {
                return $this->response->setResponse([
                    'result' => 'success',
                    'message' => 'Order successfully completed!'
                ]);
            }

            if (!$result) {
                throw new \Exception('Order error.');
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->view->render('error', ['message' => 'Error 500']);
        }
    }
}
