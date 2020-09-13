<?php
namespace application\http;

use \application\traits\Singleton;

class Server {
    use Singleton;

    private $environment = [];

    private function __construct() {
        $this->environment = $_SERVER;
    }

    public function get($key) {
        return $this->environment[$key] ?? null;
    }
}
