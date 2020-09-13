<?php
namespace application\core;

use \application\traits\Singleton;

class Config {
    use Singleton;

    private $config;

    public function __construct() {
        $this->config = include BASE_DIR . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
    }

    public function get($item) {
        return $this->config[$item] ?? null;
    }
}
