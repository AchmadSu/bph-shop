<?php

namespace App\Services\Order;

use LaravelEasyRepository\BaseService;
use App\Models\Order;

interface OrderService extends BaseService
{

    public function find($id);
    public function checkout($userId);
    public function verifyPayment(Order $order, bool $isApproved, $useTransaction = true);
    public function cancelExpiredOrders();
    public function getWaitingVerificationOrder();
}
