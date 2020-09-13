<?php
namespace application\controllers;

use \application\controllers\base\Controller;
use \application\models\CatalogModel;

class CatalogController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new CatalogModel($this->config->get('db'));
    }

    public function action_index() {
        try {
            $products = $this->model->getProducts();
            
            if (is_null($products)) {
                throw new \Exception('Catalog database query error.');
            }

            $rows = $this->model->getRowsCount();
    
            $templateVars = array_merge($this->templateVars, [
                'products' => $products,
                'rows' => $rows
            ]);

            return $this->view->render('catalog/index', $templateVars);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->view->render('error', ['message' => 'Error 500']);
        }
    }

    public function action_show() {
        try {
            $lastRowNum = (int) strip_tags($_POST['lastRowNum']);

            $products = $this->model->getProducts($lastRowNum);

            if (is_null($products)) {
                throw new \Exception('Catalog database query error.');
            }

            return $this->view->render('catalog/productsList', ['products' => $products]);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->view->render('error', ['message' => 'Error 500']);
        }
    }
}
