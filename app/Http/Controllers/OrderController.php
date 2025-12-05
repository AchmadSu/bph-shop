<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Order\OrderService;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(
        OrderService $orderService,
    ) {
        $this->orderService = $orderService;
    }

    public function checkout(Request $request)
    {
        try {
            $order = $this->orderService->checkout($request->user()->id);
            return response()->json(successResponse("Checkout successfully", $order));
        } catch (\Exception $e) {
            $response = errorResponse($e);
            return response()->json($response, $response['status_code']);
        }
    }

    public function show($id)
    {
        $rules = [
            'id' => 'required|integer|exists:orders,id',
        ];

        $errorResponse = validateFormData(['id' => $id], $rules);
        if (!empty($errorResponse)) {
            return $errorResponse;
        };

        try {
            $order = $this->orderService->find($id);
            return response()->json(successResponse("Get order {$order->order_number} successfully", $order));
        } catch (\Exception $e) {
            $response = errorResponse($e);
            return response()->json($response, $response['status_code']);
        }
    }

    public function listForUser(Request $request)
    {
        try {
            $orders = $request->user()->orders()->with('items', 'payment')->paginate(15);
            return response()->json(successResponse("Get my order list successfully", $orders->toArray(), true));
        } catch (\Exception $e) {
            $response = errorResponse($e);
            return response()->json($response, $response['status_code']);
        }
    }

    public function waitingVerifyOrders()
    {
        try {
            $order = $this->orderService->getWaitingVerificationOrder();
            return response()->json(successResponse("Get waiting verification orders successfully", $order->toArray(), true));
        } catch (\Exception $e) {
            $response = errorResponse($e);
            return response()->json($response, $response['status_code']);
        }
    }
}
