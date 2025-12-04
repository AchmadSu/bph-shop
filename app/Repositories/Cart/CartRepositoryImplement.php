<?php

namespace App\Repositories\Cart;

use LaravelEasyRepository\Implementations\Eloquent;
use App\Models\Cart;
use App\Models\CartItem;
use App\Repositories\Product\ProductRepository;

class CartRepositoryImplement extends Eloquent implements CartRepository
{

    /**
     * Model class to be used in this repository for the common methods inside Eloquent
     * Don't remove or change $this->model variable name
     * @property Model|mixed $model;
     */
    protected $model;
    private $cartItem;
    private $productRepo;

    public function __construct(Cart $model)
    {
        $this->model = $model;
        $this->cartItem = new CartItem();
        $this->productRepo = app(ProductRepository::class);
    }

    public function getActiveCart($userId)
    {
        try {
            return $this->model->firstOrCreate(['user_id' => $userId, 'status' => 'active']);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function addItem($cartId, $productId, $quantity = 1)
    {
        try {
            $product = $this->productRepo->find($productId);

            if (!$product) {
                throw new \Exception("Product not found.");
            }

            $item = $this->cartItem->where('cart_id', $cartId)->where('product_id', $productId)->first();
            if ($item) {
                $newQty = $item->quantity += $quantity;
                if ($newQty > $product->stock) {
                    throw new \Exception("Insufficient Stock for Product: {$product->name}", 400);
                }
                $item->save();
                return $item;
            }
            if ($quantity > $product->stock) {
                throw new \Exception("Insufficient Stock for Product: {$product->name}", 400);
            }
            return $this->cartItem->create(['cart_id' => $cartId, 'product_id' => $productId, 'quantity' => $quantity]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function updateItem($cartId, $productId, $quantity)
    {
        try {
            $product = $this->productRepo->find($productId);

            if (!$product) {
                throw new \Exception("Product not found.");
            }
            $item = $this->cartItem->where('cart_id', $cartId)->where('product_id', $productId)->firstOrFail();

            if ($quantity > $product->stock) {
                throw new \Exception("Insufficient Stock for Product: {$product->name}", 400);
            }

            $item->quantity = $quantity;
            $item->save();
            return $item;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function removeItem($cartId, $productId)
    {
        try {
            return $this->cartItem->where('cart_id', $cartId)->where('product_id', $productId)->delete();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function checkout(Cart $cart)
    {
        try {
            $cart->update(['status' => 'checked_out']);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
