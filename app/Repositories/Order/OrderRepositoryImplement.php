<?php

namespace App\Repositories\Order;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Order;
use Illuminate\Support\Str;

class OrderRepositoryImplement extends Eloquent implements OrderRepository
{

    /**
     * Model class to be used in this repository for the common methods inside Eloquent
     * Don't remove or change $this->model variable name
     * @property Model|mixed $model;
     */
    protected $model;

    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    public function createOrder(array $data)
    {
        if (empty($data['order_number'])) {
            $data['order_number'] = 'ORD-' . Str::upper(Str::random(8));
        }
        try {
            return $this->model->create($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function find($id)
    {
        try {
            return $this->model->findOrFail($id);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function updateStatus(Order $order, string $status)
    {
        try {
            $order->status = $status;
            $order->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getWaitingVerificationOrders()
    {
        try {
            return $this->model->where('status', 'awaiting_verification')->paginate(15);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
