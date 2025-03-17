<?php

namespace App\Repositories\Notification;

use Illuminate\Notifications\DatabaseNotification;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class NotificationRepository extends BaseRepository implements NotificationRepositoryInterface
{
  /**
   * get model
   * @return string
   */
  public function getModel()
  {
    return DatabaseNotification::class;
  }
  /**
   * get Notification list
   * @param Request $request
   * @return void
   */
  public function getNotificationList(Request $request)
  {
    try {
      DB::beginTransaction();

      $user = auth()->user();

      $query = $user->notifications();

      $notifications = $this->paginateQuery($query->getQuery(), $request->all(), 'notification');

      $unreadCount = $user->unreadNotifications()->count();
      DB::commit();

      return [
        'notifications' => $notifications,
        'unread_count' => $unreadCount
      ];
    } catch (Exception $e) {
      DB::rollBack();

      Log::error('getNotificationList: ' . $e->getMessage());
      throw new Exception('Failed to fetch notifications');
    }
  }

  /**
   * read Notification
   * @param Request $request
   * @return void
   */
  public function readNotification(Request $request)
  {
    try {
      DB::beginTransaction();

      $user = auth()->user();
      $notification = $user->notifications()->where('id', $request->id)->first();

      if (!$notification) {
        return response()->json(['error' => 'Notification not found'], 404);
        return ['message' => 'Failed to mark notification as read', 'code' => 500];
      }

      $notification->markAsRead();
      DB::commit();

      return $notification;
    } catch (Exception $e) {
      DB::rollBack();

      Log::error('readNotification: ' . $e->getMessage());
      throw new Exception('Failed to read notification');
    }
  }



  /**
   * create Notification
   * @return mixed
   */
  public function createNotification(Request $request) {}

  /**
   * update Notification
   * @return mixed
   */
  public function updateNotification(Request $request) {}

  /**
   * delete Notification
   * @return mixed
   */
  public function deleteNotification(Request $request) {}

  public function updateState(Request $request) {}

  /**
   * add Order to Notification
   * @return mixed
   */
  public function addOrderToNotification(Request $request) {}

  /**
   * get number Notifications by time
   * @param Request $request
   * @return void
   */
  public function getNumberNotificationByTime(Request $request) {}
}
