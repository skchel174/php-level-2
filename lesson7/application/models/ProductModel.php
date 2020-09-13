<?php
namespace application\models;

use \application\models\base\Model;

class ProductModel extends Model {
    public function getProduct($id) {
        try {
            $sql = "SELECT * FROM `catalog` WHERE `id` = :id";

            $result = self::$pdo->prepare($sql);
            $result->bindParam(':id', $id, \PDO::PARAM_INT);
            $result->execute();

            return $result->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    public function checkProductInCart($user_id, $product_id) {
        try {
            $sql = "SELECT `id` FROM `cart` WHERE `user_id` = :user_id AND `product_id` = :product_id";
            $result = self::$pdo->prepare($sql);
            $result->bindParam(':user_id', $user_id, \PDO::PARAM_STR);
            $result->bindParam(':product_id', $product_id, \PDO::PARAM_INT);
            $result->execute();
            
            return $result->fetch(\PDO::FETCH_NUM);
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    public function addProduct($user_id, $product_id) {
        try {
            $sql = "INSERT INTO `cart` (`user_id`, `product_id`) VALUES (:user_id, :product_id)";

            $result = self::$pdo->prepare($sql);
            $result->bindParam(':user_id', $user_id, \PDO::PARAM_STR);
            $result->bindParam(':product_id', $product_id, \PDO::PARAM_INT);
            $result->execute();

            return $result;
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }
}
