<?php

namespace App\Services\Payment;

use LaravelEasyRepository\BaseService;
use App\Models\Order;

interface PaymentService extends BaseService
{

    public function uploadProof(Order $order, $file);
    public function verifyPayment($paymentId, $verifierUserId, $approved = true, $notes = null, $useTransaction = true);
}
