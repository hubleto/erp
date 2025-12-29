<?php

namespace Hubleto\App\Community\Notifications;

use Hubleto\Framework\Core;

class Sender extends Core
{

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
    string $url = '',
    string $color = '',
    int $priority = 0
  ): array|bool {
    $idUser = $this->authProvider()->getUserId();

    if ($idTo > 0 && $idTo != $idUser) {
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
        'url' => $url,
        'color' => $color,
      ])->toArray();
      return $notification;
    } else {
      return false;
    }

  }

}
