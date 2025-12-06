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
            $products = $this->service->getAvailableProducts();
            return response()->json(successResponse("Get products successfully", $products->toArray(), true));
        } catch (\Exception $e) {
            $response = errorResponse($e);
            return response()->json($response, $response['status_code']);
        }
    }

    public function getAll()
    {
        try {
            $products = $this->service->getProducts();
            return response()->json(successResponse("Get products successfully", $products->toArray(), true));
        } catch (\Exception $e) {
            $response = errorResponse($e);
            return response()->json($response, $response['status_code']);
        }
    }

    public function show($id)
    {
        $rules = [
            'id' => 'required|integer|exists:products,id',
        ];

        $errorResponse = validateFormData(['id' => $id], $rules);
        if ($errorResponse) return $errorResponse;

        try {
            $product = $this->service->getProductById($id);
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
            'name'  => 'required|string|min:3|unique:products,name',
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

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $data['id'] = $id;

        $rules = [
            'id'          => 'required|numeric|exists:products,id',
            'name'        => 'sometimes|string|min:3|unique:products,name',
            'description' => 'sometimes|string|min:5',
            'price'       => 'sometimes|numeric|min:1000',
            'stock'       => 'sometimes|integer|min:0',
            'is_active'   => 'sometimes|boolean'
        ];

        $errorResponse = validateFormData($data, $rules);
        if ($errorResponse) return $errorResponse;
        unset($data['id']);

        try {
            $product = $this->service->updateProduct($id, $data);
            return response()->json(successResponse("Update product {$product->name} successfully", $product));
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
