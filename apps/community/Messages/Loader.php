<?php

namespace HubletoApp\Community\Messages;

class Loader extends \HubletoMain\Core\App
{

  public bool $hasCustomSettings = true;

  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);
  }

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^messages\/?$/' => Controllers\Messages::class,
      '/^messages\/inbox\/?$/' => Controllers\Inbox::class,
      '/^messages\/sent\/?$/' => Controllers\Sent::class,
      '/^messages\/all\/?$/' => Controllers\All::class,
      '/^messages\/settings\/?$/' => Controllers\Settings::class,
      '/^messages\/api\/mark-as-read\/?$/' => Controllers\Api\MarkAsRead::class,
      '/^messages\/api\/mark-as-unread\/?$/' => Controllers\Api\MarkAsUnread::class,
    ]);
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      (new Models\Message($this->main))->dropTableIfExists()->install();
      (new Models\Index($this->main))->dropTableIfExists()->install();
    }
  }

  public function getNotificationsCount(): int
  {
    $mMessage = new \HubletoApp\Community\Messages\Models\Message($this->main);
    return $mMessage->record->prepareReadQuery()
      ->where(function($q) {
        $q->where('midx.id_to', $this->main->auth->getUserId());
        $q->orWhere('midx.id_cc', $this->main->auth->getUserId());
        $q->orWhere('midx.id_bcc', $this->main->auth->getUserId());
      })
      ->whereNull('read')
      ->count()
    ;
  }

  public function parseEmailsFromString(string $emails): array
  {
    $emailsFound = [];
    $emails = str_replace(' ', '', $emails);
    $emails = str_replace(';', ',', $emails);
    foreach (explode(',', $emails) as $email) {
      if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailsFound[] = $email;
      }
    }

    return $emailsFound;
  }

  public function send(
    int|string $to, int|string $cc, int|string $bcc,
    string $subject, string $body,
    string $color = '',
    int $priority = 0
  ): array
  {
    $user = $this->main->auth->getUser();
    $idUser = $user['id'] ?? 0;
    $fromEmail = $user['email'] ?? '';

    $mUser = new \HubletoApp\Community\Settings\Models\User($this->main);
    $users = $mUser->record->get()->toArray();
    $usersByEmail = [];
    $emailsByUserId = [];
    foreach ($users as $user) {
      $usersByEmail[$user['email']] = $user['id'];
      $emailsByUserId[$user['id']] = $user['email'];
    }

    $message = [];

    if (!empty($fromEmail)) {

      if (is_string($to)) $toEmails = $this->parseEmailsFromString($to);
      else $toEmails = [ $emailsByUserId[$to] ];

      if (is_string($cc)) $ccEmails = $this->parseEmailsFromString($cc);
      else $ccEmails = [ $emailsByUserId[$cc] ];

      if (is_string($bcc)) $bccEmails = $this->parseEmailsFromString($bcc);
      else $bccEmails = [ $emailsByUserId[$bcc] ];

      $mMessage = new Models\Message($this->main);
      $mIndex = new Models\Index($this->main);

      $messageData = [
        'priority' => $priority,
        'sent' => date('Y-m-d H:i:s'),
        'from' => $fromEmail,
        'to' => join(', ', $toEmails),
        'cc' => join(', ', $ccEmails),
        'subject' => $subject,
        'body' => $body,
        'color' => $color,
      ];

      $message = $mMessage->record->create($messageData)->toArray();
      $idMessage = $message['id'] ?? 0;

      if ($idMessage > 0) {
        foreach ($toEmails as $email) {
          $idUserTo = $usersByEmail[$email] ?? 0;
          if ($idUserTo > 0) {
            $mIndex->record->create(['id_message' => $idMessage, 'id_from' => $idUser, 'id_to' => $idUserTo]);
          }
        }
        foreach ($ccEmails as $email) {
          $idUserCc = $usersByEmail[$email] ?? 0;
          if ($idUserCc > 0) {
            $mIndex->record->create(['id_message' => $idMessage, 'id_from' => $idUser, 'id_cc' => $idUserCc]);
          }
        }
        foreach ($bccEmails as $email) {
          $idUserTo = $usersByEmail[$email] ?? 0;
          if ($idUserBcc > 0) {
            $mIndex->record->create(['id_message' => $idMessage, 'id_from' => $idUser, 'id_bcc' => $idUserBcc]);
          }
        }
      }
    }

    return $message;
  }
}
