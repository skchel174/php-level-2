<?php
namespace application\core;

class View {
    private static $twig = null;

    public function __construct() {
        if (is_null(self::$twig)) {
            $loader = new \Twig_loader_Filesystem(TMPL_DIR);
            self::$twig = new \Twig_Environment($loader);
        }
    }

    public function addGlobal($key, $array) {
        self::$twig->addGlobal($key, $array);
    }

    public function render($template, $params = []) {
        $template = self::$twig->loadTemplate($template . '.tmpl');
        $response = $template->render($params);
        
        return $response;
    }
}