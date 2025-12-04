<?php

namespace App\Repositories\Cart;

use LaravelEasyRepository\Repository;
use App\Models\Cart;

interface CartRepository extends Repository
{
    public function getActiveCart($userId);
    public function addItem($cartId, $productId, $quantity = 1);
    public function updateItem($cartId, $productId, $quantity);
    public function removeItem($cartId, $productId);
    public function checkout(Cart $cart);
}
