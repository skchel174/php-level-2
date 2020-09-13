<?php 
namespace application\http;

class Response {
    public function html($data) {
        $this->setHeader('Content-type text/html;charset=utf-8');
        echo $data;
    }

    public function xml($data) {
        $this->setHeader('Content-type: text/xml;charset=utf-8');
        echo $data;
    }

    public function json($data) {
        $this->setHeader('Content-Type: application/json;charset=utf-8');
        echo json_encode($data);
    }

    public function setHeader($string) {
        header($string);
        return $this;
    }

    public function setCode($code) {
        http_response_code($code);
        return $this;
    }
}