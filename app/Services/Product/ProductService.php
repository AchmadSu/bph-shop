<?php

namespace App\Services\Product;

use LaravelEasyRepository\BaseService;

interface ProductService extends BaseService
{

    public function getProducts();
    public function getAvailableProducts();
    public function getProductById($id);
    public function createProduct(array $data);
    public function updateProduct(int $id, array $data);
    public function importFromExcel($file);
}
