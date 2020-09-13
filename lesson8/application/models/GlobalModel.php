<?php
namespace application\models;

class GlobalModel {
    private $elements = [];

    public function __construct($elements) {
        $this->elements = $elements;
    }

    public function getElements($user) {
        return [
            'title' => $this->elements['title'],
            'menu' => $this->getMenu($user),
            'user_name' => $user,
        ];
    }

    protected function getMenu($user) {
        return [
            'nav' => $this->elements['menu']['nav'],
            'user' => empty($user) ? $this->elements['menu']['unauthorized'] : $this->elements['menu']['authorized'],
            'cart' => $this->elements['menu']['cart']
        ];
    }

}
