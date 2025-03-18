<?php

namespace App\Repositories\Discount;

use App\Models\Discount;
use App\Repositories\RepositoryInterface;
use Illuminate\Http\Request;

interface DiscountRepositoryInterface extends RepositoryInterface
{
  /**
   * get Discount list
   * @param Request $request
   * @return void
   */
  public function getDiscountList(Request $request);

  /**
   * create Discount
   * @return mixed
   */
  public function createDiscount(Request $request);

  /**
   * update Discount
   * @return mixed
   */
  public function updateDiscount(Request $request,  $id);


  /**
   * Summary of getDiscountById
   * @param \Illuminate\Http\Request $request
   * @return void
   */
  public function getDiscountById(string $id);


  public function getProductsInDiscount($discountId, $request);

  /**
   * Summary of removeProductsFromDiscount
   * @param mixed $discountId
   * @param mixed $productIds
   * @return void
   */
  public function removeProductsFromDiscount($discountId, $productIds);

  /**
   * Summary of addProductsToDiscount
   * @param mixed $discountId
   * @param mixed $productIds
   * @return void
   */
  public function addProductsToDiscount($discountId, $productIds);
}
