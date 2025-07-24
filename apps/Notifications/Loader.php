<?php

namespace HubletoApp\Community\Notifications;

class Loader extends \Hubleto\Framework\App
{
  public function __construct(\HubletoMain\Loader $main)
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

    $this->main->crons->addCron(Crons\DailyDigest::class);
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      (new Models\Notification($this->main))->dropTableIfExists()->install();
    }
  }

  public function getNotificationsCount(): int
  {
    $mNotification = $this->main->di->create(\HubletoApp\Community\Notifications\Models\Notification::class);
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
  ): array {
    $user = $this->main->auth->getUser();
    $idUser = $user['id'] ?? 0;

    if ($idTo > 0) {
      $mNotification = $this->main->di->create(Models\Notification::class);
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

  public function getDailyDigestForUser(array $user)
  {
    $dailyDigest = [];
    $digestHtml = '';

    $apps = $this->main->apps->getEnabledApps();
    foreach ($apps as $appNamespace => $app) {
      $appDigest = [];

      $dailyDigestClass = '\\' . $appNamespace . '\\Controllers\\Api\\DailyDigest';
      if (class_exists($dailyDigestClass)) {
        $dailyDigestController = $this->main->di->create($dailyDigestClass);
        $dailyDigestController->user = $user;
        $appDigest = $dailyDigestController->response();
      }

      if (count($appDigest) > 0) {
        $dailyDigest[$appNamespace] = $appDigest;
      }
    }

    if (count($dailyDigest) > 0) {
      $digestHtml = '<h1>Hi ' . ($user['nick'] ?? ($user['first_name'] ?? '')) . ', here is your daily digest</h1>';
      foreach ($dailyDigest as $appNamespace => $items) {
        $digestHtml .= "<h3>{$appNamespace}</h3>";
        foreach ($items as $item) {
          $digestHtml .= "
            <div style='font-size:11pt;margin-bottom:0.25em;padding:0.25em;border:1px solid #EEEEEE;border-left:0.5em solid {$item['color']}'>
              <b>" . htmlspecialchars($item['category']) . "</b>
              <a href='{$this->main->config->getAsString('rootUrl')}/{$item['url']}'>" . htmlspecialchars($item['text']) . "</a><br/>
              <small>" . htmlspecialchars($item['description']) . "</small>
            </div>
          ";
        }
      }
    }

    return $digestHtml;
  }
}
