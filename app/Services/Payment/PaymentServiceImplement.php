<?php

namespace App\Services\Payment;

use LaravelEasyRepository\Service;
use App\Repositories\Payment\PaymentRepository;
use App\Models\Order;
use App\Repositories\Order\OrderRepository;
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

  public function verifyPayment($paymentId, $verifierUserId, $approved = true, $notes = null)
  {
    try {
      return DB::transaction(function () use ($paymentId, $approved, $verifierUserId, $notes) {
        $payment = $this->mainRepository->update($paymentId, [
          'status' => $approved ? 'verified' : 'rejected',
          'verified_by' => $verifierUserId,
          'admin_notes' => $notes
        ]);

        $order = $payment->order;
        $order->status = $approved ? 'verified' : 'cancelled';
        $order->save();

        if ($approved) {
          foreach ($order->items as $item) {
            $product = $item->product;
            $product->stock -= $item->qty;
            $product->save();
          }
        }

        return $payment->fresh();
      });
    } catch (\Exception $e) {
      throw $e;
    }
  }
}
