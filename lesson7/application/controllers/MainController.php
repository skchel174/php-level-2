<?php
namespace application\controllers;

use \application\controllers\base\Controller;

class MainController extends Controller {
    public function action_index() {
        return $this->view->render('main/index', $this->templateVars);
    }
}