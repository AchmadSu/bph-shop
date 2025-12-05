<?php

namespace App\Repositories\Order;

use LaravelEasyRepository\Repository;
use App\Models\Order;

interface OrderRepository extends Repository
{

    public function createOrder(array $data);
    public function find($id);
    public function updateStatus(Order $order, string $status);
    public function getWaitingVerificationOrders();
}
