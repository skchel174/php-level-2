<?php
namespace application\models;

use \application\core\BaseModel;

class ProductModel extends BaseModel {
    public function getProduct($product_id, $user_id = null) {
        $product = $this->getProductInfo($product_id);

        if (!is_null($user_id)) {
            $inCart = $this->isProductInCart($product_id, $user_id);
        }

        return [
            'info' => $product,
            'status' => !empty($inCart) ? 'incart' : '',
        ];
    }

    private function getProductInfo($product_id) {
        $sql = "SELECT * FROM `catalog` WHERE `id` = :product_id";

        $query = $this->connection->prepare($sql);
        $query->bindParam(':product_id', $product_id, \PDO::PARAM_INT);

        return $this->fetchAssoc($query);
    }

    private function isProductInCart($product_id, $user_id) {
        $sql = "SELECT count(*) FROM `cart` WHERE `user_id` = :user_id AND `product_id` = :product_id";

        $query = $this->connection->prepare($sql);
        $query->bindParam(':user_id', $user_id, \PDO::PARAM_STR);
        $query->bindParam(':product_id', $product_id, \PDO::PARAM_INT);

        $result = $this->fetchNum($query);

        if ($result[0] > 0) {
            return true;
        }
        return false;
    }

    public function addProduct($product_id, $user_id) {
        $sql = "INSERT INTO `cart` (`user_id`, `product_id`) VALUES (:user_id, :product_id)";

        $query = $this->connection->prepare($sql);
        $query->bindParam(':user_id', $user_id, \PDO::PARAM_STR);
        $query->bindParam(':product_id', $product_id, \PDO::PARAM_INT);

        if ($this->execute($query)) {
            return 200;
        }
        return 500;
    }
}
