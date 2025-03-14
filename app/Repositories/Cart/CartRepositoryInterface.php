<?php

namespace App\Repositories\Cart;

use App\Models\Cart;
use App\Repositories\RepositoryInterface;
use Illuminate\Http\Request;

interface CartRepositoryInterface extends RepositoryInterface
{
  /**
   * get Cart list
   * @param Request $request
   * @return void
   */
  public function getCartList(Request $request);

  /**
   * get number Carts by time
   * @param Request $request
   * @return void
   */
  public function getNumberCartByTime(Request $request);

  /**
   * create Cart
   * @return mixed
   */
  public function createCart(Request $request);

  /**
   * update Cart
   * @return mixed
   */
  public function updateCart(Request $request);

  /**
   * delete Cart
   * @return mixed
   */
  public function deleteCart(Request $request);

  /**
   * update state Cart
   * @return mixed
   */
  public function updateState(Request $request);

  /**
   * add Order to Cart
   * @return mixed
   */
  public function addOrderToCart(Request $request);
}
