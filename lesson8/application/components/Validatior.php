<?php
namespace application\components;

class Validator {
    public function boolFilter($var, $filter = FILTER_VALIDATE_BOOLEAN, $options = null) {
        return filter_var($var, $filter, $options);
    }

    public function intFilter($var, $filter = FILTER_SANITIZE_NUMBER_INT, $options = null) {
        return filter_var($var, $filter, $options);
    }

    public function stringFilter($var, $filter = FILTER_SANITIZE_STRING, $options = null) {
        return filter_var($var, $filter, $options);
    }  

    public function arrayFilter($array, $defenition) {
        return filter_var_array($array, $defenition, false);
    }
}
