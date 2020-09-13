<?php
namespace application\service;

use \application\core\BaseService;

class ProductService extends BaseService {

    /**
     * Возвращает идентификатор продукта
     * 
     * @return string
     */
    public function getProductId() {
        return $this->validator->intFilter($this->request->get('id'));
    }

    /**
     * Возвращает идентификатор пользователя, либо null
     * 
     * @return string|null
     */
    public function getUserCartId() {
        $user_id = $this->getUserId();

        if (empty($user_id)) {
            $user_id = $this->validator->stringFilter($this->session->get('cart_id'));
        }
        
        return $user_id;
    }

    /**
     * Создает, сохраняет в куках и возвращает идентификатор незарегистрированного пользователя
     * 
     * @return string
     */
    public function getUnregistredUserId() {
        $cart_id = md5(random_bytes(16));
        $this->cookie->set('cart_id', $cart_id, time() + 3600);

        return $cart_id;
    }

    /**
     * Возвращает идентификатор последнего товара, 
     * переданного пользователю со страницей каталога
     * 
     * @return int
     */
    public function getLastItem() {
        return $this->validator->intFilter($this->request->get('lastId'));
    }
}
