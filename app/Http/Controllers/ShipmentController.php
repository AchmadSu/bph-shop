<?php

namespace App\Http\Controllers;

use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use App\Services\Shipment\ShipmentService;
use Exception;

class ShipmentController extends Controller
{
    protected $shipmentService;
    protected $orderService;

    public function __construct(
        ShipmentService $shipmentService,
        OrderService $orderService
    ) {
        $this->shipmentService = $shipmentService;
        $this->orderService = $orderService;
    }

    public function readyOrders()
    {
        try {
            $shipment = $this->shipmentService->getReadyOrders();
            return response()->json(successResponse("Get packing order successfully", $shipment->toArray(), true));
        } catch (\Exception $e) {
            $response = errorResponse($e);
            return response()->json($response, $response['status_code']);
        }
    }

    public function updateStatus(Request $request, $orderId, $status)
    {
        $data = [
            "status" => $status,
            "notes" => $request->notes ?? null
        ];

        $rules = [
            'status' => 'required|in:packing,shipped,delivered',
            'notes' => 'nullable|string'
        ];

        $errorResponse = validateFormData($data, $rules);
        if ($errorResponse) return $errorResponse;

        try {
            $order = $this->orderService->find($orderId);
            $updatedOrder = $this->shipmentService->updateShipmentStatus(
                $order,
                $status,
                $request->notes ?? null
            );

            return response()->json(
                successResponse(
                    "Shipment logs for order {$order->order_number} has been change to {$status} status",
                    $updatedOrder
                )
            );
        } catch (\Exception $e) {
            $response = errorResponse($e);
            return response()->json($response, $response['status_code']);
        }
    }

    public function logs($orderId)
    {
        $data = ["order_id" => $orderId];
        $rules = ['order_id' => 'required|integer|exists:orders,id',];

        $errorResponse = validateFormData($data, $rules);
        if ($errorResponse) return $errorResponse;

        try {
            $user = auth()->user();
            if ($user->hasExactRoles('buyer')) {
                $order = $this->orderService->find($orderId);
                if ($order->user_id !== $user->id) {
                    throw new Exception("You do not have any permission to access this endpoint", 403);
                }
            }
            $shipment = $this->shipmentService->getShipmentLogs($orderId);
            return response()->json(successResponse("Get shipment log by order successfully", $shipment->toArray()));
        } catch (\Exception $e) {
            $response = errorResponse($e);
            return response()->json($response, $response['status_code']);
        }
    }
}
