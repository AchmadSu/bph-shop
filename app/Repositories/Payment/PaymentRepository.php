<?php

namespace App\Repositories\Payment;

use LaravelEasyRepository\Repository;

interface PaymentRepository extends Repository
{

    public function createPayment(array $data);
    public function findByOrder($orderId);
    public function update($id, array $data);
}
