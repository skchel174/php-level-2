<?php
namespace application\http;

use \application\traits\Singleton;

class Cookie {
    use Singleton;

    private $cookieData = [];

    private function __construct() {
        if (!empty($_COOKIE)) {
            $this->cookieData = $_COOKIE;
        }
    }

    public function get($key = null) {
        if (is_null($key)) {
            return $this->cookieData;
        }

        return $this->cookieData[$key] ?? null;
    }

    public function set($key, $value, $time) {
        setcookie($key, $value, $time, '/');
    }
    
    public function unset($key) {
        setcookie($key, '', time() - 3600, '/');
    }
}
