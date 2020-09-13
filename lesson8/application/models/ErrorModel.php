<?php
namespace application\models;

class ErrorModel {
    private $errors = [];

    public function __construct($errors) {
        $this->errors = $errors;
    }

    public function getError($code) {
        return $this->errors[$code];
    }
}
