<?php
namespace application\controllers;

use \application\core\BaseController;
use \application\models\CatalogModel;
use \application\service\ProductService;

class CatalogController extends BaseController {
    private $catelogModel,
            $service;

    public function __construct() {
        parent::__construct();
        $this->catalogModel = new CatalogModel('gallery');
        $this->service = new ProductService();
    }

    public function action_index() {
        $catalog = $this->catalogModel->getCatalog();

        $data = $this->view->render('catalog/index', ['catalog' => $catalog]);

        $this->response->html($data);
    }

    public function action_append() {
        $lastItem = $this->service->getLastItem();

        $products = $this->catalogModel->getCatalog($lastItem);

        $data = $this->view->render('catalog/productsList', ['catalog' => $products]);
        
        $this->response->html($data);
    }
}
