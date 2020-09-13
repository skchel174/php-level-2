<?php
namespace application\models;

use \application\core\BaseModel;

class CartModel extends BaseModel {
    /**
     * Получает товары, находящиеся в корзине
     * 
     * @return array
     */
    public function getProducts($user_id) {
        $sql = "SELECT `cart`.`id`, `cart`.`product_id`, `cart`.`count`, `catalog`.`name`, `catalog`.`image`, `catalog`.`price` 
        FROM `cart` INNER JOIN `catalog` ON `cart`.`product_id` = `catalog`.`id`
        WHERE `cart`.`user_id` = :user_id";

        $query = $this->connection->prepare($sql);
        $query->bindParam(':user_id', $user_id, \PDO::PARAM_STR);

        return $this->fetchAssocAll($query);
    }

    /**
     * Увеличивает на единицу количество товара в корзине,
     * в случае успеха возвращает 200 код, неудачи - 500   
     * 
     * @return integer
     */
    public function increaseProduct($user_id, $product_id) {
        $sql = "UPDATE `cart` SET `count` = `count` + 1 WHERE `user_id` = :user_id AND `product_id` = :product_id";

        $query = $this->connection->prepare($sql);
        $query->bindParam(':user_id', $user_id, \PDO::PARAM_STR);
        $query->bindParam(':product_id', $product_id, \PDO::PARAM_INT);

        if ($this->execute($query)) {
            return 200;
        }
        return 500;
    }

    /**
     * Уменьшает на единицу количество товара в корзине,
     * в случае успеха возвращает 200 код, неудачи - 500   
     * 
     * @return integer
     */
    public function decreaseProduct($user_id, $product_id) {
        $sql = "UPDATE `cart` SET `count` = `count` - 1 WHERE `user_id` = :user_id AND `product_id` = :product_id";

        $query = $this->connection->prepare($sql);
        $query->bindParam(':user_id', $user_id, \PDO::PARAM_STR);
        $query->bindParam(':product_id', $product_id, \PDO::PARAM_INT);

        if ($this->execute($query)) {
            return 200;
        }
        return 500;
    }

    /**
     * Удалает товар из корзины
     * 
     * @return integer
     */
    public function removeProduct($user_id, $product_id) {
        $sql = "DELETE FROM `cart` WHERE `user_id` = :user_id AND `product_id` = :product_id";

        $query = $this->connection->prepare($sql);
        $query->bindParam(':user_id', $user_id, \PDO::PARAM_STR);
        $query->bindParam(':product_id', $product_id, \PDO::PARAM_INT);

        if ($this->execute($query)) {
            return 200;
        }
        return 500;
    }

    /**
     * Создает заказ, возвращает сообщение с результатом операции
     * 
     * @return string
     */
    public function makeOrder($user_id) {
        if (empty($user_id)) {
            return ['result' => 'fail',
                    'message' => 'To make an order, you must to register!'];
        }

        $products = $this->getProducts($user_id);

        if (empty($products)) {
            return ['result' => 'fail', 
                    'message' => 'There are no items in the cart'];
        }

        $order_id = $this->registerOrder($user_id);

        if (!$this->setProductsInOrder($order_id, $products)) {
            return ['result' => 'fail',
                    'message' => 'Order error.'];
        }
        return ['result' => 'success',
                'message' => 'Order successfully completed!'];
    }

    /**
     * Присваивает заказу идентификатор, связанный с идентификатором пользователя,
     * возвращает идентификатор заказа
     * 
     * @return integer
     */
    protected function registerOrder($user_id) {
        $sql = "INSERT INTO `order` (`user_id`) VALUES (:user_id)";

        $query = $this->connection->prepare($sql);
        $query->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $this->execute($query);

        return (int) $this->connection->lastInsertId();
    }

    /**
     * Проходит в цикле по массиву с товарами из корзины и добавляет информацию о товаре в БД с заказом
     */
    protected function setProductsInOrder($order_id, $products) {
        foreach ($products as $product) {
            $sql = "INSERT INTO `order_products` (`order_id`, `product_id`, `count`) 
                    VALUES (:order_id, :product_id, :count)";
            
            $query = $this->connection->prepare($sql);
            $query->bindParam(':order_id', $order_id, \PDO::PARAM_INT);
            $query->bindParam(':product_id', $product['product_id'], \PDO::PARAM_INT);
            $query->bindParam(':count', $product['count'], \PDO::PARAM_INT);

            $result = $this->execute($query);

            if (!$result) {
                return false;
            }
        }
        return true;
    }

    // if (!AuthenticationController::getInstance()->isSessionExist()) {
    //     return $this->response->setResponse([
    //         'result' => 'fail',
    //         'message' => 'To make an order, you must register!'
    //     ]);
    // }

    // $user_id = AuthenticationController::getInstance()->getUserId(); 


    // if ($result) {
    //     return $this->response->setResponse([
    //         'result' => 'success',
    //         'message' => 'Order successfully completed!'
    //     ]);
    // }

    // if (!$result) {
    //     throw new \Exception('Order error.');
    // }
}
