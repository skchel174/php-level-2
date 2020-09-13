<?php
namespace application\core;

use \application\core\Config;
use \application\core\Connection;
use \application\core\Router;
use \application\http\Session;
use \application\http\Server;
use \application\http\Request;
use \application\http\Cookie;
use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;
use \application\traits\Singleton;
use \application\controllers\ErrorController;

class Application {
    use Singleton;

    private $config,
            $connection,
            $router,
            $request,
            $server,
            $session,
            $cookie,
            $logger;

    private function __construct() {
        $this->config = Config::getInstance();
        $this->server = Server::getInstance();
        $this->request = Request::getInstance();
        $this->session = Session::getInstance();
        $this->cookie = Cookie::getInstance();

        $this->router = new Router($this->config->routes());
        $this->connection = new Connection($this->config->db());
        $this->logger = new Logger('common');
        
        $this->logger->pushHandler(new StreamHandler(LOG_DIR . DIRECTORY_SEPARATOR . 'common.log', Logger::WARNING));
    }

    public function dispatch() {
        try {
            if (!$this->router->parseRoute($this->request->get('path'))) {
                throw new \Exception('404');
            }

            $route = $this->router->getRoute();
    
            $controller_name = $route['controller'];
            $action_name = $route['action'];

            $controller_class = '\\application\\controllers\\' . ucfirst($controller_name) . 'Controller';
            $action_method = 'action_' . $action_name;
            
            $controller = new $controller_class();

            $controller->before();

            $controller->$action_method();
        } catch (\Exception $e) {
            new ErrorController($e->getMessage());
        }
    }
    
    public function config() {
        return $this->config;
    }

    public function router() {
        return $this->router;
    }

    public function connection() {
        return $this->connection;
    }

    public function server() {
        return $this->server;
    }

    public function request() {
        return $this->request;
    }

    public function session() {
        return $this->session;
    }

    public function cookie() {
        return $this->cookie;
    }

    public function logger() {
        return $this->logger;
    }
}
