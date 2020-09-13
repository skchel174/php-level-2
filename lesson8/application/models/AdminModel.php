<?php
namespace application\models;

use \application\core\BaseModel;

class AdminModel extends BaseModel {
    /**
     * 
     */
    public function signin($user_id, $password) {
        if (!$password) {
            return "Enter password!";
        }

        $admin = $this->getAdminByUserId($user_id);

        if (empty($admin)) {
            return "No administrator rights!";
        }

        $hash_password = md5($password . $admin['salt']);
    
        if ($hash_password !== $admin['password']) {
            return 'Wrong password!';
        }

        return $admin;
    }


    /**
     * Возвращает данные администратора по идентификатору пользователя
     * 
     * @param integer $user_id - идннтификатор пользователя
     * @return array
     */
    protected function getAdminByUserId($user_id) {
        $sql = "SELECT * FROM `admins` WHERE `user_id` = :user_id";

        $query = $this->connection->prepare($sql);
        $query->bindParam(':user_id', $user_id, \PDO::PARAM_INT);

        return $this->fetchAssoc($query);
    }
}

