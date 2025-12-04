<?php

namespace App\Services\Payment;

use LaravelEasyRepository\Service;
use App\Repositories\Payment\PaymentRepository;
use App\Models\Order;
use App\Repositories\Order\OrderRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class PaymentServiceImplement extends Service implements PaymentService
{

  /**
   * don't change $this->mainRepository variable name
   * because used in extends service class
   */
  protected $mainRepository;
  protected $orderRepo;

  public function __construct(PaymentRepository $mainRepository, OrderRepository $orderRepo)
  {
    $this->mainRepository = $mainRepository;
    $this->orderRepo = $orderRepo;
  }

  public function uploadProof(Order $order, $file)
  {
    try {
      return DB::transaction(function () use ($order, $file) {
        $existingPayment = $this->mainRepository->findByOrder($order->id);
        if ($existingPayment) {
          throw new Exception("Payment for order {$order->order_number} existed with {$existingPayment->status} status", 400);
        }
        $path = $file->store('payments', 'public');
        $payment = $this->mainRepository->createPayment([
          'order_id' => $order->id,
          'proof_path' => getenv('APP_URL') . "storage/" . $path,
          'status' => 'pending'
        ]);
        $order->status = 'awaiting_verification';
        $order->save();
        return $payment;
      });
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function verifyPayment($paymentId, $verifierUserId, $approved = true, $notes = null, $useTransaction = true)
  {
    $action = function () use ($paymentId, $verifierUserId, $approved, $notes) {
      $payment = $this->mainRepository->update($paymentId, [
        'status'      => $approved ? 'verified' : 'rejected',
        'verified_by' => $verifierUserId,
        'admin_notes' => $notes
      ]);
      return $payment->fresh();
    };

    return $useTransaction
      ? DB::transaction(fn() => $action())
      : $action();
  }
}
