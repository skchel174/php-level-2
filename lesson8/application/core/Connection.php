<?php
namespace application\core;

class Connection {
    private $config = [];
    private $connections = [];

    public function __construct($config) {
        $this->config = $config;
    }

    public function get($dbname) {
        if (!array_key_exists($dbname, $this->connections)) {
            $params = $this->config[$dbname];

            $this->connections[$dbname] = new \PDO(
                                        'mysql:host=' . $params['host'] . ';dbname=' . $params['dbname'],
                                        $params['username'],
                                        $params['passwd'],
                                        [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);  
        }

        return $this->connections[$dbname];
    }
}
