<?php

namespace App\Services\Order;

use App\Models\Order;
use LaravelEasyRepository\Service;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Cart\CartRepository;
use App\Repositories\Product\ProductRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderServiceImplement extends Service implements OrderService
{

  /**
   * don't change $this->mainRepository variable name
   * because used in extends service class
   */
  protected $mainRepository;
  protected $orderModel;
  protected $cartRepo;
  protected $productRepo;

  public function __construct(
    OrderRepository $mainRepository,
    CartRepository $cartRepo,
    ProductRepository $productRepo,
  ) {
    $this->mainRepository = $mainRepository;
    $this->cartRepo = $cartRepo;
    $this->productRepo = $productRepo;
    $this->orderModel = new Order();
  }

  public function find($id)
  {
    try {
      $auth = auth()->user();
      $order = $this->mainRepository->find($id);
      if (!$order) {
        throw new Exception("Order not found", 404);
      }
      if ($auth->hasExactRoles('buyer') && ($order->user_id !== $auth->id)) {
        throw new Exception("You do not have any permission to access this endpoint", 403);
      }
      $order->load('items.product');
      $order->load('shipmentLogs');
      return $order;
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function checkout($userId)
  {
    return DB::transaction(function () use ($userId) {
      $cart = $this->cartRepo->getActiveCart($userId);
      $cart->load('items.product');

      if ($cart->items->isEmpty()) {
        throw new Exception('Cart is empty', 400);
      }

      $total = 0;
      foreach ($cart->items as $item) {
        $total += $item->quantity * (float)$item->product->price;
      }

      $order = $this->mainRepository->createOrder([
        'user_id' => $userId,
        'order_number' => 'ORD-' . Str::upper(Str::random(8)),
        'cart_id' => $cart->id,
        'total_amount' => $total,
        'status' => 'pending_payment',
        'expired_at' => Carbon::now()->addHours(24),
      ]);

      foreach ($cart->items as $item) {
        $order->items()->create([
          'product_id' => $item->product->id,
          'product_name' => $item->product->name,
          'price' => $item->product->price,
          'quantity' => $item->quantity,
        ]);
      }
      $this->cartRepo->checkout($cart);
      return $order->fresh('items');
    });
  }

  public function verifyPayment(Order $order, bool $isApproved, $useTransaction = true)
  {
    $action = function () use ($order, $isApproved) {

      if ($order->status !== 'awaiting_verification' && $order->status !== 'pending_payment') {
        throw new \Exception('Order is not in verification state');
      }

      if ($isApproved) {
        foreach ($order->items as $item) {
          $product = $this->productRepo->find($item->product_id);
          if ($product->stock < $item->quantity) {
            throw new \Exception("Insufficient stock for product {$product->name}");
          }
        }

        foreach ($order->items as $item) {
          $ok = $this->productRepo->reduceStock($item->product_id, $item->quantity);
          if (!$ok) {
            throw new \Exception("Failed to reduce stock for product id {$item->product_id}");
          }
        }
      }

      $status = $isApproved ? 'verified' : 'cancelled';
      $this->mainRepository->updateStatus($order, $status);

      return $order->fresh();
    };

    return $useTransaction
      ? DB::transaction(fn() => $action())
      : $action();
  }

  public function cancelExpiredOrders()
  {
    $expiredOrders = $this->orderModel
      ->whereIn('status', ['pending_payment', 'awaiting_verification'])
      ->whereNotNull('expired_at')
      ->where('expired_at', '<', now())
      ->update(['status' => 'cancelled']);

    return $expiredOrders;
  }


  public function getWaitingVerificationOrder()
  {
    try {
      $order = $this->mainRepository->getWaitingVerificationOrders();
      if (!$order) {
        throw new Exception("No waiting verification orders this time", 404);
      }
      $order->load('items.product');
      $order->load('payment');
      return $order;
    } catch (\Exception $e) {
      throw $e;
    }
  }
}
