<?php

namespace App\Repositories\Product;

use LaravelEasyRepository\Repository;

interface ProductRepository extends Repository
{
    public function getAll();
    public function getAvailableProduct();
    public function find($id);
    public function createProduct(array $data);
    public function reduceStock(int $productId, int $qty);
    public function bulkInsert(array $rows);
}
