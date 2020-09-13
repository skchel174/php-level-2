<?php
namespace application\controllers;

use \application\core\BaseController;
use \application\models\ErrorModel;

class ErrorController extends BaseController{
    protected $model;

    public function __construct($code) {
        parent::__construct();

        $this->model = new ErrorModel($this->config->errors());
        $error = $this->model->getError($code);

        $this->logger->error(implode(' ', $error));

        $html = $this->view->render('error/index', ['error' => $error]);
        $this->response->setCode($code)->html($html);
    }    
}
