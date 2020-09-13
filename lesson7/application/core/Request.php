<?php
namespace application\core;

use \application\traits\Singleton;

class Request {
    use Singleton;

    private static $getData = [];
    private static $postData = [];
    private static $filesData = [];
    private static $cookieData = [];

    private function __construct() {
        if (!empty($_GET)) {
            self::$getData = $_GET;
        }

        if (!empty($_POST)) {
            self::$postData = $_POST;
        }
        
        if (!empty($_FILES)) {
            self::$filesData = $_FILES;
        }

        if (!empty($_COOKIE)) {
            self::$cookieData = $_COOKIE;
        }
    }

    public function get($key) {
        return self::$getData[$key] ?? null;
    }

    public function post($key) {
        return self::$postData[$key] ?? null;
    }

    public function files($key) {
        return self::$filesData[$key] ?? null;
    }

    public function cookie($key) {
        return self::$cookieData[$key] ?? null;
    }
}
