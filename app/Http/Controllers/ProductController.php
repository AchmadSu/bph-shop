<?php

namespace App\Http\Controllers;

use App\Services\Product\ProductService;
use Exception;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        try {
            $user = auth()->user();
            $products = match (true) {
                $user->hasExactRoles('buyer') => $this->service->getAvailableProducts(),
                $user->hasAnyRole(['admin', 'cs1', 'cs2']) => $this->service->getProducts(),
                default => throw new Exception("You do not have any permission to access this endpoint", 403)
            };
            return response()->json(successResponse("Get products successfully", $products, true));
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
        if ($errorResponse) return $errorResponse;

        try {
            $product = $this->service->find($id);
            return response()->json(successResponse("Get product {$product->name} successfully", $product));
        } catch (\Exception $e) {
            $response = errorResponse($e);
            return response()->json($response, $response['status_code']);
        }
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $rules = [
            'name'  => 'required|string|min:3',
            'description'  => 'nullable|string|min:5',
            'price' => 'required|numeric|min:1000',
            'stock' => 'required|integer|min:0'
        ];

        $errorResponse = validateFormData($data, $rules);
        if ($errorResponse) return $errorResponse;
        try {
            $product = $this->service->createProduct($data);
            return response()->json(successResponse("Create product successfully", $product));
        } catch (\Exception $e) {
            $response = errorResponse($e);
            return response()->json($response, $response['status_code']);
        }
    }

    public function importExcel(Request $request)
    {
        $data = $request->all();
        $required = ['file'];

        $errorResponse = checkArrayRequired($data, $required);
        if ($errorResponse) return $errorResponse;

        $rules = [
            'file' => 'required|file|mimes:xlsx,csv'
        ];

        $errorResponse = validateFormData($data, $rules);
        if ($errorResponse) return $errorResponse;

        try {
            $this->service->importFromExcel($data['file']);
            return response()->json(successResponse("Product imported successfully"));
        } catch (\Exception $e) {
            $response = errorResponse($e);
            return response()->json($response, $response['status_code']);
        }
    }
}
