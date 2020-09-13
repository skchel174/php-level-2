<?php
namespace application\core;

use \application\components\Validator;

class Router {
    private $routes,
            $controller,
            $action;

    public function __construct($routes) {
        $this->routes = $routes;
    }

    public function parseRoute($path) {
        $validator = new Validator();

        $path = $validator->stringFilter($path);

        if (empty($path)) {
            $this->controller = $this->routes['default']['controller'];
            $this->action = $this->routes['default']['action'];
            return true;
        }

        $path = explode('/', $path);
        
        if (!array_key_exists($path[0], $this->routes)) {
            return false;
        }

        $this->controller = $path[0];

        if (empty($path[1])) {
            $this->action = $this->routes[$this->controller]['action'][0];
        } else if (in_array($path[1], $this->routes[$this->controller]['action'])) {
            $this->action = $path[1];
        } else {
            return false;
        }

        return true;
    }
    
    public function getRoute() {
        return [
            'controller' => $this->controller,
            'action' => $this->action
        ];
    }
}