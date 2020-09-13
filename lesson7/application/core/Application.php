<?php
namespace application\core;

use \application\core as Core;
use \application\views\View;
use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;
use \application\traits\Singleton;

class Application {
    use Singleton;

    private $session;
    private $config;
    private $router;
    private $request;
    private $response;
    private $logger;

    public function __construct() {
        $this->session = Core\Session::getInstance();
        $this->config = Core\Config::getInstance();
        $this->request = Core\Request::getInstance();
        $this->response = Core\Response::getInstance();
        $this->router = Core\Router::getInstance();
        $this->logger = new Logger('common');
        $this->logger->pushHandler(new StreamHandler(BASE_DIR . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'common.log', Logger::WARNING));
    }

    public function session() {
        return self::getInstance()->session;
    }

    public function config() {
        return self::getInstance()->config;
    }

    public function request() {
        return self::getInstance()->request;
    }

    public function response() {
        return self::getInstance()->response;
    }

    public function router() {
        return self::getInstance()->router;
    }

    public function logger() {
        return self::getInstance()->logger;
    }

    public function dispatch() {
        try {
            $route = $this->router->getRoute();

            if (!class_exists($route['controller'])) {
                throw new \Exception('Class ' . $route['controller'] . ' doesn\'t exist.');
            }
            $controller = new $route['controller']();
            
            $controller->before();

            if (!method_exists($controller, $route['action'])) {
                throw new \Exception('Action ' . $route['action'] . ' doesn\'t exist');
            }
            $action = $route['action'];

            $controller->$action();
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            View::getInstance()->render('error', ['message' => 'Error 404']);
        }
    }
}
