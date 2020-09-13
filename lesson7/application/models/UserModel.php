<?php
namespace application\models;

use \application\models\base\Model;

class UserModel extends Model {
    public function checkUserSession($user_id, $session_key) {
        try {
            $sql = "SELECT `users`.`id`, `users`.`name`, `user_sessions`.`session_key` FROM `users` 
            INNER JOIN `user_sessions` ON `users`.`id` = `user_sessions`.`user_id` 
            WHERE `users`.`id` = :user_id";

            $result = self::$pdo->prepare($sql);
            $result->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
            $result->execute();

            $user = $result->fetch(\PDO::FETCH_ASSOC);

            if ($user['session_key'] !== $session_key) {
                return false;
            }

            return $user;
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    public function getUserSession($user_id) {
        try {
            $sql = "SELECT `session_key` FROM `user_sessions` WHERE `user_id` = :user_id";

            $result = self::$pdo->prepare($sql);
            $result->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
            $result->execute();

            return $result->fetch(\PDO::FETCH_NUM)[0];
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    public function setUserSession($user_id) {
        try {
            $session_key = md5(random_bytes(16));

            $sql = "INSERT INTO `user_sessions` (`user_id`, `session_key`) VALUES (:user_id, :session_key)";

            $result = self::$pdo->prepare($sql);
            $result->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
            $result->bindValue(':session_key', $session_key, \PDO::PARAM_STR);
            $result->execute();

            return $session_key;
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }
    
    public function unsetUserSession($user_id, $session_key) {
        try {
            $sql = "DELETE FROM `user_sessions` WHERE `user_id` = :user_id AND `session_key` = :session_key";

            $result = self::$pdo->prepare($sql);
            $result->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
            $result->bindValue(':session_key', $session_key, \PDO::PARAM_STR);
            $result->execute();
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    public function getVisitedPages($user_id) {
        try {
            $sql = "SELECT `page_name` FROM `visited_pages` WHERE `user_id` = :user_id LIMIT 5";

            $result = self::$pdo->prepare($sql);
            $result->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
            $result->execute();

            $pages = [];
            while($page = $result->fetch(\PDO::FETCH_ASSOC)) {
                $pages[] = $page['page_name'];
            }
            return $pages;
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    public function setVisitedPage($user_id, $page_name) {
        try {
            $sql = "INSERT INTO `visited_pages` (`user_id`, `page_name`) VALUES (:user_id, :page_name)";

            $result = self::$pdo->prepare($sql);
            $result->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
            $result->bindValue(':page_name', $page_name, \PDO::PARAM_STR);
            $result->execute();
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    public function getUserById($user_id) {
        try {
            $sql = "SELECT `id`, `name`, `login`, `email` FROM `users` WHERE `id` = :user_id";

            $result = self::$pdo->prepare($sql);
            $result->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
            $result->execute();

            return $result->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    public function getUserByLogin($login) {
        try {
            $sql = "SELECT `id`, `name`, `password`, `salt` FROM `users` WHERE `login` = :login";

            $result = self::$pdo->prepare($sql);
            $result->bindValue(':login', $login, \PDO::PARAM_STR);
            $result->execute();

            return $result->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    public function signout($user_id) {
        try {
            $sql = "DELETE FROM `user_sessions` WHERE `user_id` = :user_id";
            
            $result = self::$pdo->prepare($sql);
            $result->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
            $result->execute();
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }   

    public function signup($name, $login, $password, $email) {
        try {
            $salt = md5(random_bytes(16));
            $hash_password = md5($password . $salt);
            
            $sql = "INSERT INTO `users` (`login`, `password`, `salt`, `name`, `email`) 
                    VALUES (:login, :hash_password, :salt, :name, :email)";
    
            $result = self::$pdo->prepare($sql); 
            $result->bindParam(':login', $login, \PDO::PARAM_STR);
            $result->bindParam(':hash_password', $hash_password, \PDO::PARAM_STR);
            $result->bindParam(':salt', $salt, \PDO::PARAM_STR);
            $result->bindParam(':name', $name, \PDO::PARAM_STR);
            $result->bindParam(':email', $email, \PDO::PARAM_STR);
            $result->execute();
            
            return self::$pdo->lastInsertId();
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    public function checkLogin($login) {
        try {
            $sql = "SELECT `id` FROM `users` WHERE `login` = :login";

            $result = self::$pdo->prepare($sql);
            $result->bindValue(':login', $login, \PDO::PARAM_STR);
            $result->execute();

            return $result->fetch(\PDO::FETCH_NUM);
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }   
}
