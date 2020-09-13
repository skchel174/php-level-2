<?php
namespace application\http;

use \application\traits\Singleton;

class Request {
    use Singleton;

    private $getData = [],
            $postData = [],
            $filesData = [];

    private function __construct() {
        if (!empty($_GET)) {
            $this->getData = $_GET;
        }

        if (!empty($_POST)) {
            $this->postData = $_POST;
        }
        
        if (!empty($_FILES)) {
            $this->filesData = $_FILES;
        }
    }

    public function get($key = null) {
        if (is_null($key)) {
            return $this->getData;
        }
        return $this->getData[$key] ?? null;
    }

    public function post($key = null) {
        if (is_null($key)) {
            return $this->postData;
        }
        return $this->postData[$key] ?? null;
    }

    public function files($key = null) {
        if (is_null($key)) {
            return $this->filesData;
        }
        return $this->filesData[$key] ?? null;
    }

    
}
