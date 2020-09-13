<?php
namespace application\core;

use \application\traits\Singleton;

class Config {
    use Singleton;

    public function db() {
        return include(CONF_DIR . DIRECTORY_SEPARATOR . 'db.php');
    }

    public function globalElements() {
        return include(CONF_DIR . DIRECTORY_SEPARATOR . 'globalElements.php');
    }

    public function errors() {
        return include(CONF_DIR . DIRECTORY_SEPARATOR . 'errors.php');
    }
    
    public function routes() {
        return include(CONF_DIR . DIRECTORY_SEPARATOR . 'routes.php');
    }

    public function orderStatus() {
        return include(CONF_DIR . DIRECTORY_SEPARATOR . 'orderStatus.php');
    }
}
