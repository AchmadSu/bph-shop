<?php

namespace App\Services\Product;

use LaravelEasyRepository\Service;
use App\Repositories\Product\ProductRepository;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
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

  public function updateProduct(int $id, array $data)
  {
    try {
      return DB::transaction(function () use ($id, $data) {
        return $this->mainRepository->updateProduct($id, $data);
      });
    } catch (\Exception $e) {
      throw $e;
    }
  }

  public function importFromExcel($file)
  {
    try {
      $reader = ReaderEntityFactory::createReaderFromFile($file->getClientOriginalName());
      $reader->open($file->getRealPath());

      $rows = [];
      $isHeader = true;
      $headers = [];

      foreach ($reader->getSheetIterator() as $sheet) {
        foreach ($sheet->getRowIterator() as $row) {
          $cells = $row->toArray();

          if ($isHeader) {
            $headers = array_map('strtolower', $cells);
            $isHeader = false;
            continue;
          }

          $rowData = array_combine($headers, $cells);

          $rows[] = [
            'name'        => $rowData['name'] ?? null,
            'description' => $rowData['description'] ?? null,
            'price'       => (float)$rowData['price'] ?? 0,
            'stock'       => (int)$rowData['stock'] ?? 0,
            'created_at'  => now(),
            'updated_at'  => now(),
          ];
        }
      }

      DB::transaction(function () use ($rows) {
        $this->mainRepository->bulkInsert($rows);
      });
    } catch (\Exception $e) {
      throw $e;
    }
  }
}
