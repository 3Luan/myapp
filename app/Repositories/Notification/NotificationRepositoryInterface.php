<?php

namespace App\Repositories\Notification;

use App\Models\Notification;
use App\Repositories\RepositoryInterface;
use Illuminate\Http\Request;

interface NotificationRepositoryInterface extends RepositoryInterface
{
  /**
   * get Notification list
   * @param Request $request
   * @return void
   */
  public function getNotificationList(Request $request);

  /**
   * get number Notifications by time
   * @param Request $request
   * @return void
   */
  public function getNumberNotificationByTime(Request $request);

  /**
   * create Notification
   * @return mixed
   */
  public function createNotification(Request $request);

  /**
   * update Notification
   * @return mixed
   */
  public function updateNotification(Request $request);

  /**
   * delete Notification
   * @return mixed
   */
  public function deleteNotification(Request $request);

  /**
   * update state Notification
   * @return mixed
   */
  public function updateState(Request $request);

  /**
   * add Order to Notification
   * @return mixed
   */
  public function addOrderToNotification(Request $request);

  /**
   * read Notification
   * @param Request $request
   * @return void
   */
  public function readNotification(Request $request);
}
