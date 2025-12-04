<?php

namespace App\Services\Shipment;

use LaravelEasyRepository\Service;
use App\Repositories\Shipment\ShipmentRepository;
use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\DB;

class ShipmentServiceImplement extends Service implements ShipmentService
{

  /**
   * don't change $this->mainRepository variable name
   * because used in extends service class
   */
  protected $mainRepository;

  public function __construct(ShipmentRepository $mainRepository)
  {
    $this->mainRepository = $mainRepository;
  }

  public function getReadyOrders()
  {
    try {
      return $this->mainRepository->getReadyOrders();
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function updateShipmentStatus(Order $order, string $status, ?string $notes = null)
  {
    $allowedFlow = [
      'verified'   => 'packing',
      'packing'    => 'shipped',
      'shipping' => 'delivered',
    ];

    if (!array_key_exists($order->status, $allowedFlow) || $allowedFlow[$order->status] !== $status) {
      throw new Exception("Invalid changing status from '{$order->status}' to '{$status}'", 400);
    }

    return DB::transaction(function () use ($order, $status, $notes) {
      $this->mainRepository->createLog($order->id, $status, $notes);

      if ($status === 'packing') {
        $this->mainRepository->updateOrderStatus($order, 'packing');
      }

      if ($status === 'shipped') {
        $this->mainRepository->updateOrderStatus($order, 'shipping');
      }

      if ($status === 'delivered') {
        $this->mainRepository->updateOrderStatus($order, 'completed');
      }
      return $order->fresh('shipmentLogs');
    });
  }

  public function getShipmentLogs(int $orderId)
  {
    try {
      return $this->mainRepository->getLogsByOrder($orderId);
    } catch (\Exception $e) {
      throw $e;
    }
  }
}
