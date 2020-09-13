<?php
namespace application\controllers;

use \application\core\BaseController;

class MainController extends BaseController {
    public function action_index() {    
        $data = $this->view->render('main/index');
        $this->response->setCode(200)->html($data);
    }
}
