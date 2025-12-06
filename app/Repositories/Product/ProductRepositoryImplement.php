<?php

namespace App\Repositories\Product;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Product;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProductRepositoryImplement extends Eloquent implements ProductRepository
{

    /**
     * Model class to be used in this repository for the common methods inside Eloquent
     * Don't remove or change $this->model variable name
     * @property Model|mixed $model;
     */
    protected $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        try {
            $product = $this->model->paginate(15);
            return $product;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getAvailableProduct()
    {
        try {
            $product = $this->model
                ->where('is_active', true)
                ->where('stock', '>', 0)
                ->paginate($param['paginate'] ?? 8);
            return $product;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function find($id)
    {
        try {
            return $this->model->findOrFail($id);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function createProduct(array $data)
    {
        try {
            return $this->model->create($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function updateProduct(int $id, array $data)
    {
        try {
            $product = $this->find($id);
            $product->update($data);
            return $product;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function reduceStock(int $productId, int $qty)
    {
        try {
            return $this->model->where('id', $productId)->where('stock', '>=', $qty)
                ->decrement('stock', $qty);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function bulkInsert(array $rows)
    {
        try {
            $this->model->upsert(
                $rows,
                ['name'],
                ['description', 'price', 'stock']
            );
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
