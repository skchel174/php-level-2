<?php
namespace application\components;

/**
 * Объект класса Helper может вызываться перед выполнением метода объекта-контроллера, 
 * ссылку на который он сохраняет в собственное свойство.
 * Метод next выполняе метод контроллера переданный ему в качестве аргумента, 
 * и возвращает сылку на собственный объект, благодаря чему может вызываться цепочка методов контроллера.
 * В случает если метод контроллера возвращает false, ссылка на его объект удлаяется из свойства хелпера,
 * цепочка методов контроллера прерывается.
 */

class Helper {
    private $controller = null;
    private $arg = null;

    /**
     * Принимает объект класса, относящегося к контроллерам
     * 
     * @param object - объект, оборачиваемый в Helper
     */
    public function __construct($controller) {
        $this->controller = $controller;
    }

    /**
     * Метод проверяет наличие внутри хелпера объекта-контроллера, 
     * передает его в метод execute вместе со свойством arg.
     * Если свойство arg равно false, удаляет ссылку на вложенный объект из своства controller
     * Возвращает ссылку на собственный объект.
     * 
     * @param $action - название метода объекта, харнящегося внутри Helper
     * @return object $this - ссылка на объект класса Helper
     */
    public function next($action) {
        if (!is_null($this->controller)) {
            $this->execute($action, $this->arg);
        }

        if ($this->arg === false) {
            $this->controller = null;
            $this->arg = null;
        }

        return $this;
    }   

    /**
     * Принимает название метода вложенного объекта-контроллера и аргумент, который переадет в этот метод. 
     * Вызывает внутри себя указанный метод, и сохраняет его результат в свойство arg.
     * 
     * @param $action - название метода объекта, харнящегося внутри Helper
     * @return void
     */
    protected function execute($action, $arg) {
        $result = $this->controller->$action($arg);
        $this->arg = $result;
    }
}
