<?php

namespace Hubleto\App\Community\Notifications;

class Loader extends \Hubleto\Framework\App
{

  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^notifications\/?$/' => Controllers\Notifications::class,
      '/^notifications\/inbox\/?$/' => Controllers\Inbox::class,
      '/^notifications\/sent\/?$/' => Controllers\Sent::class,
      '/^notifications\/all\/?$/' => Controllers\All::class,
      '/^notifications\/api\/mark-as-read\/?$/' => Controllers\Api\MarkAsRead::class,
      '/^notifications\/api\/mark-as-unread\/?$/' => Controllers\Api\MarkAsUnread::class,
    ]);

    $this->cronManager()->addCron(Crons\DailyDigest::class);
  }

  /**
   * [Description for installTables]
   *
   * @param int $round
   * 
   * @return void
   * 
   */
  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Notification::class)->dropTableIfExists()->install();
    }
  }

  /**
   * [Description for getNotificationsCount]
   *
   * @return int
   * 
   */
  public function getNotificationsCount(): int
  {
    $mNotification = $this->getModel(Models\Notification::class);
    return $mNotification->record->prepareReadQuery()
      ->where('id_to', $this->getService(AuthProvider::class)->getUserId())
      ->whereNull('datetime_read')
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
    $user = $this->getService(AuthProvider::class)->getUser();
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
