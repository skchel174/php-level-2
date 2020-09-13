<?php
namespace application\views;

use \application\traits\Singleton;
use \application\core\Application;

class View {
    use Singleton;

    private static $twig = null;
    private $logger;

    public function __construct() {
        $this->logger = Application::getInstance()->logger();

        if (is_null(self::$twig)) {
            $loader = new \Twig_loader_Filesystem(APP_DIR . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'templates');
            self::$twig = new \Twig_Environment($loader);
        }
    }

    public function render($template, $params = []) {
        try {
            $template = self::$twig->loadTemplate($template . '.tmpl');
            $html = $template->render($params);
            
            if (!$html) {
                throw new \Exception("Error render template $template.");
            }
            
            echo $html;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            View::getInstance()->render('error', ['message' => "Error 500"]);
        }
    }
}