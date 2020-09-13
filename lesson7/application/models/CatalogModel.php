<?php
namespace application\models;

use \application\models\base\Model;

class CatalogModel extends Model {
    public function getProducts($startRow = 0) {
        try {
            $sql = "SELECT * FROM `catalog` WHERE `instock` = 1 LIMIT :startRow, 3";
            $result = self::$pdo->prepare($sql);
            $result->bindParam(':startRow', $startRow, \PDO::PARAM_INT);
            $result->execute();
            return $result->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    public function getRowsCount() {
        try {
            $sql = "SELECT COUNT(*) FROM `catalog` WHERE `instock` = 1";
            $result = self::$pdo->query($sql);
            return $result->fetch(\PDO::FETCH_NUM)[0];
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }
}
