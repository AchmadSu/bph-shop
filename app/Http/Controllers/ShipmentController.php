<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\Shipment\ShipmentService;

class ShipmentController extends Controller
{
    protected $shipmentService;

    public function __construct(ShipmentService $shipmentService)
    {
        $this->shipmentService = $shipmentService;
    }

    public function readyOrders()
    {
        return response()->json($this->shipmentService->getReadyOrders());
    }

    public function updateStatus(Request $request, $orderId, $status)
    {
        $order = Order::findOrFail($orderId);

        $updatedOrder = $this->shipmentService->updateShipmentStatus(
            $order,
            $status,
            $request->notes ?? null
        );

        return response()->json([
            'message' => " Shipment updated to {$status}",
            'data' => $updatedOrder
        ]);
    }

    public function logs($orderId)
    {
        return response()->json($this->shipmentService->getShipmentLogs($orderId));
    }
}
