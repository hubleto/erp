<?php

namespace Hubleto\App\Community\Notifications;

use Hubleto\Framework\Core;

class Counter extends Core
{

  /**
   * [Description for myUnread]
   *
   * @return int
   * 
   */
  public function myUnread(): int
  {
    $mNotification = $this->getModel(Models\Notification::class);
    return $mNotification->record->prepareReadQuery()
      ->where('id_to', $this->authProvider()->getUserId())
      ->whereNull('datetime_read')
      ->count()
    ;
  }

  /**
   * [Description for myRead]
   *
   * @return int
   * 
   */
  public function myRead(): int
  {
    $mNotification = $this->getModel(Models\Notification::class);
    return $mNotification->record->prepareReadQuery()
      ->where('id_to', $this->authProvider()->getUserId())
      ->whereNotNull('datetime_read')
      ->count()
    ;
  }

  /**
   * [Description for myAll]
   *
   * @return int
   * 
   */
  public function myAll(): int
  {
    $mNotification = $this->getModel(Models\Notification::class);
    return $mNotification->record->prepareReadQuery()
      ->where('id_to', $this->authProvider()->getUserId())
      ->count()
    ;
  }

  /**
   * [Description for send]
   *
   * @param int $category
   * @param array $tags
   * @param int $idTo
   * @param string $subject
   * @param string $body
   * @param string $color
   * @param int $priority
   * 
   * @return array
   * 
   */
  public function send(
    int $category,
    array $tags,
    int $idTo,
    string $subject,
    string $body,
    string $color = '',
    int $priority = 0
  ): array {
    $user = $this->getService(\Hubleto\Framework\AuthProvider::class)->getUser();
    $idUser = $user['id'] ?? 0;

    if ($idTo > 0) {
      $mNotification = $this->getModel(Models\Notification::class);
      $notification = $mNotification->record->create([
        'priority' => $priority,
        'category' => $category,
        'tags' => json_encode($tags),
        'datetime_sent' => date('Y-m-d H:i:s'),
        'id_from' => $idUser,
        'id_to' => $idTo,
        'subject' => $subject,
        'body' => $body,
        'color' => $color,
      ])->toArray();
    }

    return $notification;
  }

}
