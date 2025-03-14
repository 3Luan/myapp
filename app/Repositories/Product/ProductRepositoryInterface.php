<?php

namespace App\Repositories\Product;

use App\Models\Product;
use App\Repositories\RepositoryInterface;
use Illuminate\Http\Request;

interface ProductRepositoryInterface extends RepositoryInterface
{
  /**
   * get Product list
   * @param Request $request
   * @return void
   */
  public function getProductList(Request $request);

  /**
   * get number Products by time
   * @param Request $request
   * @return void
   */
  public function getNumberProductByTime(Request $request);

  /**
   * create Product
   * @return mixed
   */
  public function createProduct(Request $request);

  /**
   * delete Product
   * @return mixed
   */
  public function deleteProduct(Request $request);

  /**
   * update Product
   * @return mixed
   */
  public function updateProduct(Request $request,  $id);

  /**
   * import Product
   * @return mixed
   */
  public function importProduct(Request $request);
}
