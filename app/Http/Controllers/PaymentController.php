<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Payment\PaymentService;
use App\Services\Order\OrderService;
use Exception;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $orderRepo;
    protected $paymentService;
    protected $orderService;

    public function __construct(
        PaymentService $paymentService,
        OrderService $orderService
    ) {
        $this->paymentService = $paymentService;
        $this->orderService = $orderService;
    }

    public function uploadProof(Request $request, $orderId)
    {
        try {
            $order = $this->orderService->find($orderId);
            if ($order->user_id !== $request->user()->id) {
                throw new Exception("You do not have any permission to access this endpoint", 403);
            }
            $data = $request->all();
            $required = ['proof'];

            $errorResponse = checkArrayRequired($data, $required);
            if ($errorResponse) return $errorResponse;


            $rules = [
                'proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:4096'
            ];

            $errorResponse = validateFormData($data, $rules);
            if ($errorResponse) return $errorResponse;

            $payment = $this->paymentService->uploadProof($order, $data['proof']);
            return response()->json(successResponse("Upload payment evidence successfully", $payment));
        } catch (\Exception $e) {
            $response = errorResponse($e);
            return response()->json($response, $response['status_code']);
        }
    }

    public function verify(Request $request, $paymentId)
    {
        $data = $request->all();
        $required = ['approved', 'notes'];

        $errorResponse = checkArrayRequired($data, $required);
        if ($errorResponse) return $errorResponse;


        $rules = [
            'approved' => 'required|boolean',
            'notes' => 'nullable|string'
        ];
        $errorResponse = validateFormData($data, $rules);
        if ($errorResponse) return $errorResponse;

        try {
            $payment = DB::transaction(function () use ($request, $paymentId, $data) {
                $user = $request->user();
                $payment = $this->paymentService->verifyPayment($paymentId, $user->id, $data['approved'], $data['notes'], false);
                $order = $payment->order;
                $this->orderService->verifyPayment($order, $data['approved'], false);
                return $payment->fresh();
            });
            return response()->json(successResponse("Verify payment successfully", $payment));
        } catch (\Exception $e) {
            $response = errorResponse($e);
            return response()->json($response, $response['status_code']);
        }
    }
}
