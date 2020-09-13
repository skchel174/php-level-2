<?php
namespace application\core;

use \application\traits\Singleton;

class Response {
    use Singleton;

    public function setCookie($key, $value, $time) {
        setcookie($key, $value, $time, '/');
    }

    public function unsetCookie($key) {
        setcookie($key, '', time() - 3600, '/');
    }

    public function setResponse($response = []) {
        echo json_encode($response);
    }
}
