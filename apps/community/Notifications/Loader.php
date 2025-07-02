<?php

namespace HubletoApp\Community\Notifications;

class Loader extends \HubletoMain\Core\App
{

  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);
  }

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^notifications\/?$/' => Controllers\Notifications::class,
      '/^notifications\/inbox\/?$/' => Controllers\Inbox::class,
      '/^notifications\/sent\/?$/' => Controllers\Sent::class,
      '/^notifications\/all\/?$/' => Controllers\All::class,
      '/^notifications\/api\/mark-as-read\/?$/' => Controllers\Api\MarkAsRead::class,
      '/^notifications\/api\/mark-as-unread\/?$/' => Controllers\Api\MarkAsUnread::class,
    ]);
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      (new Models\Notification($this->main))->dropTableIfExists()->install();
    }
  }

  public function getNotificationsCount(): int
  {
    $mNotification = new \HubletoApp\Community\Notifications\Models\Notification($this->main);
    return $mNotification->record->prepareReadQuery()
      ->where('id_to', $this->main->auth->getUserId())
      ->whereNull('datetime_read')
      ->count()
    ;
  }

  public function send(
    int $category,
    array $tags,
    int $idTo,
    string $subject,
    string $body,
    string $color = '',
    int $priority = 0
  ): array
  {
    $user = $this->main->auth->getUser();
    $idUser = $user['id'] ?? 0;

    if ($idTo > 0) {
      $mNotification = new Models\Notification($this->main);
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
