<?php

namespace App\Repositories\Product;

use LaravelEasyRepository\Repository;

interface ProductRepository extends Repository
{
    public function paginate($perPage = 15);
    public function find($id);
    public function createProduct(array $data);
    public function update($id, array $data);
    public function reduceStock(int $productId, int $qty);
}
