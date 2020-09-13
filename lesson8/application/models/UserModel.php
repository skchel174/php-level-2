<?php
namespace application\models;

use \application\core\BaseModel;

class UserModel extends BaseModel {

    /**
     * Проверяет, соответствует ли идентификатор пльзовательской сессии,
     * полученной из куки, идентификатору, сохраненному в БД.
     * 
     * @return array|bool
     */
    public function checkUserSession($user_session) {
        if (!$user_session) {
            return false;
        }

        $sql = "SELECT `users`.`id`, `users`.`name`, `user_sessions`.`session_key` FROM `users` 
        INNER JOIN `user_sessions` ON `users`.`id` = `user_sessions`.`user_id` 
        WHERE `users`.`id` = :user_id";

        $query = $this->connection->prepare($sql);
        $query->bindParam(':user_id', $user_session['user_id'], \PDO::PARAM_INT);

        $user = $this->fetchAssoc($query);

        if ($user['session_key'] !== $user_session['session_key']) {
            return false;
        }

        return $user;
    }

    /**
     * Получает данные пользователя.
     * 
     * @param integer - идентификатор пользователя
     * @return array
     */
    public function getProfile($user_id) {
        $sql = "SELECT `id`, `name`, `login`, `email` FROM `users` WHERE `id` = :user_id";

        $query = $this->connection->prepare($sql);
        $query->bindParam(':user_id', $user_id, \PDO::PARAM_INT);

        return $this->fetchAssoc($query);
    }

    /**
     * Проверяет наличие пользователя в БД администраторов
     * 
     * @param integer $user_id - идентификатор пользователя
     * @return integer
     */
    public function getUserRules($user_id) {
        
        $sql = "SELECT count(*) FROM `admins` WHERE `user_id` = :user_id";

        $query = $this->connection->prepare($sql);
        $query->bindParam(':user_id', $user_id, \PDO::PARAM_INT);

        return $this->fetchNum($query)[0];
    }

    /**
     * Проверяет форму авторизации.
     * Если поле не не прошло валидацию и пустое, возвращает сообщение.
     * В случае успеха возвращает массив данных формы.
     * 
     * @return array|string
     */
    public function checkUserRequest($request) {
        foreach ($request as $key => $value) {
            if (empty($value)) {
                return "Field $key not valid!";
            }
        }
        
        return $request;
    }

    /**
     * Возвращает результат проверки данных формы авторизации.
     * Проверяет галичие логина и правильность пароля.
     * Если включен checkbox remember, создает пользовательскую сессию.
     * 
     * @return array|string
     */
    public function signin($request) {
        $data = $this->checkUserRequest($request);

        if (!is_array($data)) {
            return $data;
        }

        $user = $this->getUserByLogin($data['login']);

        if (!$user) {
            return "User with login $login not exist!";
        }

        $hash_password = md5($data['password'] . $user['salt']);
    
        if ($hash_password !== $user['password']) {
            return 'Wrong password!';
        }

        if (isset($data['remember']) && $data['remember'] === 'on') {
            $session_key = $this->getUserSessionKey($user['id']);
            if (!$session_key) {
                $user['session_key'] = $this->setUserSessionKey($user['id']);
            } else {
                $user['session_key'] = $session_key;
            }
            $user['remember'] = 'on';
        } else {
            $user['remember'] = false;
        }

        return $user;
    }

    /**
     * Получает идентификатр пользовательской сессии.
     * При его отсутствии, возвращает false.
     * 
     * @return string|bool
     */

    protected function getUserSessionKey($user_id) {
        $sql = "SELECT `session_key` FROM `user_sessions` WHERE `user_id` = :user_id";

        $query = $this->connection->prepare($sql);
        $query->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $result = $this->fetchAssoc($query);

        if (!$result) {
            return false;
        }

        return $result;
    }
    
    /**
     * Получает пользователя по его логину.
     * 
     * @return array
     */
    public function getUserByLogin($login) {
        $sql = "SELECT `id`, `name`, `password`, `salt` FROM `users` WHERE `login` = :login";

        $query = $this->connection->prepare($sql);
        $query->bindParam(':login', $login, \PDO::PARAM_STR);

        return $this->fetchAssoc($query);
    }

    /**
     * Выполняет проверку данных формы регистрации.
     * Если логин пользователя унивкален, сохраняет данные в БД.
     * Если включен checkbox remember, создает пользовательскую сессию.
     * 
     * @return array|string
     */
    public function signup($request) {
        $data = $this->checkUserRequest($request);

        if (!is_array($data)) {
            return $data;
        }

        if ($data['password'] != $data['repeated']) {
            return 'Password confirmation error!';
        }

        $isLoginExist = $this->checkLogin($data['login']);

        if ($isLoginExist[0] > 0) {
            return 'Login is already in use!';
        }

        $salt = md5(random_bytes(16));
        $hash_password = md5($data['password'] . $salt);
        
        $sql = "INSERT INTO `users` (`login`, `password`, `salt`, `name`, `email`) 
                VALUES (:login, :hash_password, :salt, :name, :email)";

        $query = $this->connection->prepare($sql); 
        $query->bindParam(':login', $data['login'], \PDO::PARAM_STR);
        $query->bindParam(':hash_password', $hash_password, \PDO::PARAM_STR);
        $query->bindParam(':salt', $salt, \PDO::PARAM_STR);
        $query->bindParam(':name', $data['name'], \PDO::PARAM_STR);
        $query->bindParam(':email', $data['email'], \PDO::PARAM_STR);

        $this->execute($query);
        
        $data['id'] = $this->connection->lastInsertId();

        if (isset($data['remember']) && $data['remember'] == 'on') {
            $data['session_key'] = $this->setUserSessionKey($data['id']);
        } else {
            $data['remember'] = false;
        }

        return $data;
    }

    /**
     * Проверяет уникальность логина.
     * 
     * @return array
     */
    protected function checkLogin($login) {
        $sql = "SELECT count(*) FROM `users` WHERE `login` = :login";

        $query = $this->connection->prepare($sql);
        $query->bindParam(':login', $login, \PDO::PARAM_STR);

        return $this->fetchNum($query);
    } 
    
    /**
     * Создает идентификатор пользовательской сессии.
     * 
     * @return string
     */
    protected function setUserSessionKey($user_id) {
        $session_key = md5(random_bytes(16));

        $sql = "INSERT INTO `user_sessions` (`user_id`, `session_key`) VALUES (:user_id, :session_key)";

        $query = $this->connection->prepare($sql);
        $query->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $query->bindParam(':session_key', $session_key, \PDO::PARAM_STR);

        $this->execute($query);
        return $session_key;
    }

    /**
     * Удалает пользовательскую сессию.
     * 
     * @return int $user_id - идентификатор пользователя
     * @return bool
     */
    public function signout($user_id) {
        $sql = "DELETE FROM `user_sessions` WHERE `user_id` = :user_id";
        
        $query = $this->connection->prepare($sql);
        $query->bindParam(':user_id', $user_id, \PDO::PARAM_INT);

        $this->execute($query);
    }

    /**
     * Получает из БД 5 последних страниц, посещенных пользователем
     * 
     * @return int $user_id - идентификатор пользователя
     * @return array
     */
    public function getVisitedPages($user_id) {
        $sql = "SELECT `page_name` FROM `visited_pages` WHERE `user_id` = :user_id LIMIT 5";

        $query = $this->connection->prepare($sql);
        $query->bindParam(':user_id', $user_id, \PDO::PARAM_INT);

        return $this->fetchNumAll($query);
    }

    /**
     * Сохраняет в БД адрес страницы, посещенной пользователем
     * 
     * @return int $user_id - идентификатор пользователя
     * @return string $page_name - адрес страницы
     * @return void
     */
    public function setVisitedPage($user_id, $page_name) {
        $sql = "INSERT INTO `visited_pages` (`user_id`, `page_name`) VALUES (:user_id, :page_name)";

        $query = $this->connection->prepare($sql);
        $query->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $query->bindValue(':page_name', $page_name, \PDO::PARAM_STR);

        $this->execute($query);
    }
}

