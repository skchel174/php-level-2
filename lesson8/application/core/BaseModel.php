<?php
namespace application\core;

use \application\core\Application;

abstract class BaseModel {
    protected $connection,
              $logger;
    
    public function __construct($dbname) {
        $this->connection = Application::getInstance()->connection()->get($dbname);
        $this->logger = Application::getInstance()->logger();
    }

    protected function execute($query) {
        try {
            return $query->execute();
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
        }
    }  

    protected function fetchNum($query) {
        try {
            $query->execute();
            return $query->fetch(\PDO::FETCH_NUM);
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
        }
    }    

    protected function fetchNumAll($query) {
        try {
            $query->execute();
            return $query->fetchAll(\PDO::FETCH_COLUMN, 0);
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
        }
    }     

    protected function fetchAssoc($query) {
        try {
            $query->execute();
            return $query->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
        }
    } 

    protected function fetchAssocAll($query) {
        try {
            $query->execute();
            return $query->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
