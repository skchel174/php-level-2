<?php
namespace application\models;

use \application\core\BaseModel;

class CatalogModel extends BaseModel {
    public function getCatalog($startItem = 0) {
        $count = $this->getItemsCount();
        $products = $this->getProducts($startItem);

        if (is_null($count) || is_null($products)) {
            return null;
        }
        
        return [
            'count' => $count,
            'products' => $products,
        ];
    }

    protected function getItemsCount() {
        $sql = "SELECT COUNT(*) FROM `catalog` WHERE `instock` = 1";

        $result = $this->connection->query($sql);

        return $result->fetch(\PDO::FETCH_NUM)[0];
    }

    protected function getProducts($startItem) {
        $sql = "SELECT * FROM `catalog` WHERE `instock` = 1 LIMIT :startItem, 3";

        $result = $this->connection->prepare($sql);
        $result->bindParam(':startItem', $startItem, \PDO::PARAM_INT);
        $result->execute();

        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }
}
