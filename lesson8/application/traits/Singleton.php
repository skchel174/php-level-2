<?php
namespace application\traits;

trait Singleton {
    private static $instance = null;

    private function __constrcut() {}

    private function __clone() {}

    /**
     * Метод проверяет в свойстве instance ссылки на объект,
     * если ее там нет, создает объект класса.
     * Возвращает ссылку на объект класса.
     * 
     * @return Object
     */
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
