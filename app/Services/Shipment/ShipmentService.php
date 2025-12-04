<?php

namespace App\Services\Shipment;

use LaravelEasyRepository\BaseService;
use App\Models\Order;

interface ShipmentService extends BaseService
{

    public function getReadyOrders();
    public function updateShipmentStatus(Order $order, string $status, ?string $notes = null);
    public function getShipmentLogs(int $orderId);
}
