<?php
namespace application\models\base;

use application\core\Application;

abstract class Model {
    protected static $pdo = null;
    protected static $config = null;
    protected $logger;

    public function __construct($config) {
        $this->logger = Application::getInstance()->logger();

        if (is_null(self::$config)) {
            self::$config = $config;
        }

        if (is_null(self::$pdo)) {
            try {
                self::$pdo = new \PDO(
                    'mysql:host=' . self::$config['host'] . ';dbname=' . self::$config['dbname'],
                    self::$config['username'],
                    self::$config['passwd'],
                    [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);    
            } catch (\PDOException $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
