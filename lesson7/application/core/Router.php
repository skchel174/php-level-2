<?php
namespace application\core;

use \application\traits\Singleton;

class Router {
    use Singleton;

    public function getRoute() { 
        $path = strip_tags($_GET['path']);
        
        $route = explode('/', $path);

        $controller = !empty($route[0]) ? $route[0] : 'main';

        $action = !empty($route[1]) ? $route[1] : 'index';

        return [
            'controller' => '\\application\\controllers\\' . ucfirst($controller) . 'Controller',
            'action' => 'action_' . $action
        ];
    }
}
