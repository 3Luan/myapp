<?php

namespace App\Repositories\Order;

use App\Models\Order;
use App\Repositories\RepositoryInterface;
use Illuminate\Http\Request;

interface OrderRepositoryInterface extends RepositoryInterface
{

  /**
   * get Orders by User
   * @param Request $request
   * @return void
   */
  public function getOrdersByUser(Request $request);

  /**
   * get Order list
   * @param Request $request
   * @return void
   */
  public function getOrderList(Request $request);

  /**
   * get number Orders by time
   * @param Request $request
   * @return void
   */
  public function getNumberOrderByTime(Request $request);

  /**
   * create Order
   * @return mixed
   */
  public function createOrder(Request $request);

  /**
   * update Order
   * @return mixed
   */
  public function updateOrder(Request $request);
}
