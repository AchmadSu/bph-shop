<?php

namespace App\Services\Cart;

use LaravelEasyRepository\Service;
use App\Repositories\Cart\CartRepository;
use App\Repositories\Product\ProductRepository;
use Exception;

class CartServiceImplement extends Service implements CartService
{

  /**
   * don't change $this->mainRepository variable name
   * because used in extends service class
   */
  protected $mainRepository;
  protected $productRepo;

  public function __construct(CartRepository $mainRepository, ProductRepository $productRepo)
  {
    $this->mainRepository = $mainRepository;
    $this->productRepo = $productRepo;
  }

  public function addToCart($userId, $productId, $qty = 1)
  {
    try {
      $cart = $this->mainRepository->getActiveCart($userId);
      $product = $this->productRepo->find($productId);

      if (empty($product)) {
        throw new \Exception("Product not found", 404);
      }

      if (!$product->is_active) {
        throw new \Exception("Inactive Product", 403);
      }
      return $this->mainRepository->addItem($cart->id, $productId, $qty);
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function updateItem($id, $productId, $qty = 1)
  {
    try {
      $product = $this->productRepo->find($productId);

      if (empty($product)) {
        throw new \Exception("Product not found", 404);
      }

      if (!$product->is_active) {
        throw new \Exception("Inactive product", 400);
      }

      return $this->mainRepository->updateItem($id, $productId, $qty);
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function removeItem($cart, $productId)
  {
    try {
      $product = $this->productRepo->find($productId);

      if (empty($product)) {
        throw new \Exception("Product not found", 404);
      }

      if (!$product->is_active) {
        throw new \Exception("Inactive product", 400);
      }
      $items = $cart->items;
      $filtered = collect($items)->where('product_id', $productId)->values()->first();
      if (empty($filtered)) {
        throw new Exception("Cart has no item with product: {$product->name}", 400);
      }
      return $this->mainRepository->removeItem($cart->id, $productId);
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function getCart($userId)
  {
    $cart = $this->mainRepository->getActiveCart($userId);
    $cart->load('items.product');
    return $cart;
  }
}
