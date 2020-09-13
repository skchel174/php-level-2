<?php
namespace application\models;

use \application\models\base\Model;

class CartModel extends Model {
    public function getProducts($user_id) {
        try {
            $sql = "SELECT `cart`.`id`, `cart`.`product_id`, `cart`.`count`, `catalog`.`name`, `catalog`.`image`, `catalog`.`price` 
            FROM `cart` INNER JOIN `catalog` ON `cart`.`product_id` = `catalog`.`id`
            WHERE `cart`.`user_id` = :user_id";

            $result = self::$pdo->prepare($sql);
            $result->bindParam(':user_id', $user_id, \PDO::PARAM_STR);
            $result->execute();

            return $result->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    public function increaseProduct($user_id, $product_id) {
        try {
            $sql = "UPDATE `cart` SET `count` = `count` + 1 WHERE `user_id` = :user_id AND `product_id` = :product_id";

            $result = self::$pdo->prepare($sql);
            $result->bindParam(':user_id', $user_id, \PDO::PARAM_STR);
            $result->bindParam(':product_id', $product_id, \PDO::PARAM_INT);
            return $result->execute();
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    public function decreaseProduct($user_id, $product_id) {
        try {
            $sql = "UPDATE `cart` SET `count` = `count` - 1 WHERE `user_id` = :user_id AND `product_id` = :product_id";

            $result = self::$pdo->prepare($sql);
            $result->bindParam(':user_id', $user_id, \PDO::PARAM_STR);
            $result->bindParam(':product_id', $product_id, \PDO::PARAM_INT);
            return $result->execute();
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    public function removeProduct($user_id, $product_id) {
        try {
            $sql = "DELETE FROM `cart` WHERE `user_id` = :user_id AND `product_id` = :product_id";

            $result = self::$pdo->prepare($sql);
            $result->bindParam(':user_id', $user_id, \PDO::PARAM_STR);
            $result->bindParam(':product_id', $product_id, \PDO::PARAM_INT);
            return $result->execute();
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    public function makeOrder($user_id) {
        try {
            $sql = "SELECT `cart`.`product_id`, `cart`.`count` FROM `cart` WHERE `user_id` = :user_id";

            $result = self::$pdo->prepare($sql);
            $result->bindParam(':user_id', $user_id, \PDO::PARAM_STR);
            $result->execute();

            $products = $result->fetchAll(\PDO::FETCH_ASSOC);

            if (!$products) {
                return false;
            }

            $sql = "INSERT INTO `order` (`user_id`) VALUES (:user_id)";

            $result = self::$pdo->prepare($sql);
            $result->bindParam(':user_id', $user_id, \PDO::PARAM_STR);
            $result->execute();     

            $order_id = (int) self::$pdo->lastInsertId();

            if (!$order_id) {
                return false;
            }

            foreach ($products as $product) {
                $product_id = $product['product_id'];
                $count = $product['count'];

                $sql = "INSERT INTO `order_products` (`order_id`, `product_id`, `count`) VALUES (:order_id, :product_id, :count)";
                
                $result = self::$pdo->prepare($sql);
                $result->bindParam(':order_id', $order_id, \PDO::PARAM_INT);
                $result->bindParam(':product_id', $product_id, \PDO::PARAM_INT);
                $result->bindParam(':count', $count, \PDO::PARAM_INT);
                $result = $result->execute();
            }

            return $result;
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }    
    }
}
