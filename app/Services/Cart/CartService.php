<?php

namespace App\Services\Cart;

use LaravelEasyRepository\BaseService;

interface CartService extends BaseService
{

    public function addToCart($userId, $productId, $qty = 1);
    public function getCart($userId);
    public function updateItem($id, $productId, $qty = 1);
    public function removeItem($id, $productId);
}
