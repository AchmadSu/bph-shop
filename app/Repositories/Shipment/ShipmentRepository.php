<?php

namespace App\Repositories\Shipment;

use LaravelEasyRepository\Repository;
use App\Models\Order;

interface ShipmentRepository extends Repository
{

    public function getReadyOrders();
    public function createLog(int $orderId, string $status, ?string $notes);
    public function updateOrderStatus(Order $order, string $status);
    public function getLogsByOrder(int $orderId);
}
