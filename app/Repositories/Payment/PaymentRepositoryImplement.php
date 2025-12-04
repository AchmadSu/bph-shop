<?php

namespace App\Repositories\Payment;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Payment;

class PaymentRepositoryImplement extends Eloquent implements PaymentRepository
{

    /**
     * Model class to be used in this repository for the common methods inside Eloquent
     * Don't remove or change $this->model variable name
     * @property Model|mixed $model;
     */
    protected $model;

    public function __construct(Payment $model)
    {
        $this->model = $model;
    }

    public function createPayment(array $data)
    {
        try {
            return $this->model->create($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function findByOrder($orderId)
    {
        try {
            return $this->model->where('order_id', $orderId)->first();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function update($id, array $data)
    {
        try {
            $p = $this->model->findOrFail($id);
            $p->update($data);
            return $p;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
