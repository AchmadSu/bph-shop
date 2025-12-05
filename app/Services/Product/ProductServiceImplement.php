<?php

namespace App\Services\Product;

use LaravelEasyRepository\Service;
use App\Repositories\Product\ProductRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class ProductServiceImplement extends Service implements ProductService
{

  /**
   * don't change $this->mainRepository variable name
   * because used in extends service class
   */
  protected $mainRepository;

  public function __construct(ProductRepository $mainRepository)
  {
    $this->mainRepository = $mainRepository;
  }

  public function getProducts()
  {
    try {
      return $this->mainRepository->getAll();
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function getAvailableProducts()
  {
    try {
      return $this->mainRepository->getAvailableProduct();
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function getProductById($id)
  {
    try {
      $user = auth()->user();
      $product = $this->mainRepository->find($id);
      $unAvailableProduct = ($product->stock <= 0 || !$product->is_active);
      if ($user->hasExactRoles('buyer') && $unAvailableProduct) {
        throw new Exception("You do not have any permission to access this endpoint", 403);
      }
      return $product;
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function createProduct(array $data)
  {
    try {
      return DB::transaction(function () use ($data) {
        return $this->mainRepository->createProduct($data);
      });
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function importFromExcel($file)
  {
    try {
      $rows = [];
      $data = \Maatwebsite\Excel\Facades\Excel::toArray([], $file)[0];

      foreach ($data as $row) {
        $rows[] = [
          'name' => $row['name'] ?? null,
          'description' => $row['description'] ?? null,
          'price' => $row['price'] ?? 0,
          'stock' => $row['stock'] ?? 0,
          'created_at' => now(),
          'updated_at' => now(),
        ];
      }

      return DB::transaction(function () use ($rows) {
        return $this->mainRepository->bulkInsert($rows);
      });
    } catch (\Exception $e) {
      throw $e;
    }
  }
}
