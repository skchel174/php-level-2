<?php
namespace application\controllers;

use \application\core\BaseController;
use \application\models\ProductModel;
use \application\service\ProductService;

class ProductController extends BaseController {
    protected $model,
              $user_id,
              $product_id;

    public function __construct() {
        parent::__construct();
        $this->model = new ProductModel('gallery');
        $this->service = new ProductService();

        $this->user_id = $this->service->getUserId();
        $this->product_id = $this->service->getProductId();
    }

    public function action_index() {
        $product = $this->model->getProduct($this->product_id, $this->user_id);
        
        $data = $this->view->render('product/index', ['product' => $product]);

        $this->response->html($data);
    }

    public function action_add() {
        $user_id = $this->user_id;
        
        if (!$user_id) {
            $user_id = $this->service->getUnregistredUserId();
        }

        $result = $this->model->addProduct($this->product_id, $user_id);
        $this->response->setCode($result);
    }


}
