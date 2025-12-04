<?php

namespace App\Http\Controllers;

use App\Repositories\Cart\CartRepository;
use Illuminate\Http\Request;
use App\Services\Cart\CartService;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function show(Request $request)
    {
        try {
            $cart = $this->cartService->getCart($request->user()->id);
            return response()->json(successResponse("Get cart successfully", $cart));
        } catch (\Exception $e) {
            $response = errorResponse($e);
            return response()->json($response, $response['status_code']);
        }
    }

    public function add(Request $request)
    {
        $data = $request->all();
        $required = ['product_id', 'qty'];

        $errorResponse = checkArrayRequired($data, $required);
        if (!empty($errorResponse)) {
            return $errorResponse;
        };

        $rules = [
            'product_id' => 'required|integer|exists:products,id',
            'qty' => 'required|integer|min:1'
        ];
        $errorResponse = validateFormData($data, $rules);
        if (!empty($errorResponse)) {
            return $errorResponse;
        };

        try {
            $item = $this->cartService->addToCart($request->user()->id, $data['product_id'], $data['qty'] ?? 1);
            return response()->json(successResponse("Add product to cart successfully", $item));
        } catch (\Exception $e) {
            $response = errorResponse($e);
            return response()->json($response, $response['status_code']);
        }
    }

    public function update(Request $request)
    {
        $data = $request->all();
        $required = ['product_id', 'qty'];
        $errorResponse = checkArrayRequired($data, $required);
        if (!empty($errorResponse)) {
            return $errorResponse;
        };

        $rules = [
            'product_id' => 'required|integer|exists:products,id',
            'qty' => 'required|integer|min:1'
        ];

        $errorResponse = validateFormData($data, $rules);
        if (!empty($errorResponse)) {
            return $errorResponse;
        };

        try {
            $cart = $this->cartService->getCart($request->user()->id);
            $item = $this->cartService->updateItem($cart->id, $data['product_id'], $data['qty']);
            return response()->json(successResponse("Update cart successfully", $item));
        } catch (\Exception $e) {
            $response = errorResponse($e);
            return response()->json($response, $response['status_code']);
        }
    }

    public function remove(Request $request)
    {
        $data = $request->all();
        $required = ['product_id'];
        $errorResponse = checkArrayRequired($data, $required);
        if (!empty($errorResponse)) {
            return $errorResponse;
        };

        $rules = [
            'product_id' => 'required|integer|exists:products,id',
        ];
        $errorResponse = validateFormData($data, $rules);
        if (!empty($errorResponse)) {
            return $errorResponse;
        };

        try {
            $cart = $this->cartService->getCart($request->user()->id);
            $this->cartService->removeItem($cart, $data['product_id']);
            return response()->json(successResponse("Remove product from cart successfully"));
        } catch (\Exception $e) {
            $response = errorResponse($e);
            return response()->json($response, $response['status_code']);
        }
    }
}
