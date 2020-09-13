<?php
namespace application\controllers;

use \application\controllers\base\Controller;
use \application\models\OrderModel;

class OrderController extends Controller {
    private $status;

    public function __construct() {
        parent::__construct();
        $this->model = new OrderModel($this->config->get('db'));
        $this->status = [
            1 => 'awaiting',
            2 => 'in working',
            3 => 'completed',
            4 => 'revoke'
        ];
    }

    public function getForUser(int $user_id) {
        try {
            $orders = $this->model->getUserOrders($user_id);

            if (is_null($orders)) {
                throw new \Exception("Error getting order for user with id: '$user_id'");
            }
    
            if ($orders === false) {
                return null;
            }

            $result = $this->prepareUserOrder($orders);

            if (!$result) {
                throw new \Exception("Error getting order for user with id: '$user_id'");
            }
    
            return $result;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            $this->view::getInstance()->render('error', ['message' => 'Error 500']);
        }
    }

    protected function prepareUserOrder(&$orders) {
        foreach ($orders as $key => $order) {
            $orders[$key]['status'] = $this->getOrderStatus($order['status']);
            $orders[$key]['products'] = $this->model->getOrderProducts($order['user_id'], $order['id']);
        }
        return $orders;
    }

    protected function getOrderStatus(int $index) {
        return $this->status[$index];
    } 
}
