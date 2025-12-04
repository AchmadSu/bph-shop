<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Product\ProductRepository;

class ProductController extends Controller
{
    protected $repo;

    public function __construct(ProductRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 12);
        return $this->repo->paginate($perPage);
    }

    public function show($id)
    {
        return $this->repo->find($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'sku' => 'required|string|unique:products,sku',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'is_active' => 'sometimes|boolean'
        ]);

        return response()->json($this->repo->create($data), 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'sometimes|string',
            'sku' => "sometimes|string|unique:products,sku,{$id}",
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean'
        ]);

        return response()->json($this->repo->update($id, $data));
    }
}
