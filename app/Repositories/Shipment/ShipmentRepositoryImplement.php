<?php

namespace App\Repositories\Shipment;

use App\Models\Order;
use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\ShipmentLog;

class ShipmentRepositoryImplement extends Eloquent implements ShipmentRepository
{

    /**
     * Model class to be used in this repository for the common methods inside Eloquent
     * Don't remove or change $this->model variable name
     * @property Model|mixed $model;
     */
    protected $model;
    protected $order;

    public function __construct(ShipmentLog $model)
    {
        $this->model = $model;
        $this->order = new Order();
    }

    public function getReadyOrders()
    {
        return $this->order->where('status', 'paid')->get();
    }

    public function createLog(int $orderId, string $status, ?string $notes)
    {
        try {
            return $this->model->create([
                'order_id' => $orderId,
                'status'   => $status,
                'notes'    => $notes,
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function updateOrderStatus(Order $order, string $status)
    {
        try {
            $order->update(['status' => $status]);
            return $order;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getLogsByOrder(int $orderId)
    {
        try {
            return $this->model->where('order_id', $orderId)->orderBy('created_at')->get();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
