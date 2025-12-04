<?php

namespace App\Repositories\Product;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Product;
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

    public function paginate($perPage = 15)
    {
        try {
            $product = $this->model->where('is_active', true)->paginate($perPage) ?? [];
            return $product;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function find($id)
    {
        try {
            return $this->model->findOrFail($id);
        } catch (ModelNotFoundException) {
            [];
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

    public function update($id, array $data)
    {
        try {
            $p = $this->find($id);
            $p->update($data);
            return $p;
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
}
