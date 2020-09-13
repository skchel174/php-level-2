<?php
namespace application\controllers;

use \application\controllers\base\Controller;
use \application\controllers\Authentication;
use \application\models\ProductModel;

class ProductController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new ProductModel($this->config->get('db'));
    }

    public function action_index() {
        try {
            $product_id = (int) $this->request->get('id');

            $product = $this->model->getProduct($product_id);
            
            if (is_null($product)) {
                throw new \Exception("Product with id '$id' doesn't exist.");
            }

            $vars = ['product' => $product];

            if ($this->isProductInCart($product['id'])) {
                $vars['status'] = 'incart';
            }
            
            $templateVars = array_merge($this->templateVars, $vars);

            return $this->view->render('catalog/product', $templateVars);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->view->render('error', ['message' => "Error 500"]);
        }
    }

    protected function isProductInCart($product_id) {
        $user_id = AuthenticationController::getInstance()->getUserId();
        
        if (!is_null($user_id)) {
            $check = $this->model->checkProductInCart($user_id, $product_id);
            
            if (is_null($check)) {
                throw new \Exception("Error while check product in cart.");
            }
        }

        return $check;
    }

    public function action_add() {
        try {
            $product_id = (int) $this->request->post('product');
            $user_id = AuthenticationController::getInstance()->getUserId();

            if (is_null($user_id)) {
                $user_id = AuthenticationController::getInstance()->setUnregisteredUserId();
            }

            $result = $this->model->addProduct($user_id, $product_id);
            
            if (!$result) {
                throw new \Exception("Cart add product error.");
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->view->render('error', ['message' => "Error 500"]);
        }  
    }
}
