<?php
namespace application\models;

use \application\core\BaseModel;

class OrderModel extends BaseModel {
    public function __construct($dbname, $status) {
        parent::__construct($dbname);
        $this->status = $status;
    }

    /**
     * Возвращает массив с заказами пользователя и товарами в них
     * 
     * @param integer $user_id - идентификатор пользователя
     * @return array|null
     */
    public function getUserOrders($user_id) {
        $sql = "SELECT * FROM `order` WHERE `order`.`user_id` = :user_id ORDER BY `status`";

        $query = $this->connection->prepare($sql);
        $query->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $orders = $this->fetchAssocAll($query);
        
        if (empty($orders)) {
            return null;
        }

        return $this->prepareOrders($orders);
    }

    /**
     * Возвращает администратору информацию о всех заказах
     * 
     * @return array|null
     */
    public function getAllOrders() {
        $sql = "SELECT * FROM `order` ORDER BY `status`";

        $query = $this->connection->prepare($sql);
        $orders = $this->fetchAssocAll($query);

        if (empty($orders)) {
            return null;
        }

        return $this->prepareOrders($orders);       
    }

    /**
     * Добавляет в каждый заказ информацию о его статусе и товарах
     * 
     * @param array $orders - массив с заказами, передается по ссылке
     * @return array
     */
    public function prepareOrders(&$orders) {
        foreach ($orders as $key => $order) {
            $orders[$key]['status'] = $this->getOrderStatus($order['status']);
            $orders[$key]['statuses'] = $this->getOrderStatuses($order['status']);
            $orders[$key]['products'] = $this->getOrderProducts($order['user_id'], $order['id']);
        }
        return $orders; 
    }

    /**
     * Возвращает массив с товарами, находящимися в заказе
     * 
     * @param integer $user_id - идентификатор пользователя
     * @param integer $oreder_id - идентификатор заказа
     * @return array
     */
    public function getOrderProducts($user_id, $order_id) {
        $sql = "SELECT `catalog`.`id`, `catalog`.`name`, `catalog`.`price`, `catalog`.`image` FROM `order_products`
        INNER JOIN `order` ON `order_products`.`order_id` = `order`.`id`
        INNER JOIN `catalog` ON `order_products`.`product_id` = `catalog`.`id`
        WHERE `order`.`user_id` = :user_id AND `order`.`id` = :order_id";

        $query = $this->connection->prepare($sql);
        $query->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $query->bindParam(':order_id', $order_id, \PDO::PARAM_INT);

        return $this->fetchAssocAll($query);
    }

    /**
     * Возвращает статус заказа
     * 
     * @param unteger $index - индекс статуса заказа
     * @return array
     */
    public function getOrderStatus($index) {
        return $this->status[$index];
    }

    /**
     * Возвращает массив со статусами, в качестве первого элемента текущий статус
     * 
     * @param unteger $index - индекс статуса заказа
     * @return array
     */
    public function getOrderStatuses($index) {
        $status = $this->status;
        unset($status[$index]);
        return [$index => $this->status[$index]] + $status;
    }

    /**
     * Изменяет статус заказа в БД, возвращает код результата выполнения операции
     * 
     * @param array $status - массив с id заказа и индексом статуса
     * @return integer
     */
    public function setOrderStatus($status) {
        $sql = "UPDATE `order` SET `status` = :status WHERE `id` = :id";

        $query = $this->connection->prepare($sql);
        $query->bindParam(':id', $status['id'], \PDO::PARAM_INT);
        $query->bindParam(':status', $status['status'], \PDO::PARAM_INT);

        $result = $this->execute($query);
        if (!$result) {
            return 500;
        }

        return 200;
    }
}
