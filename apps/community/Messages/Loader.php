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
      '/^messages(\/(?<recordId>\d+))?\/?$/' => Controllers\Messages::class,
      '/^messages\/settings\/?$/' => Controllers\Settings::class,
    ]);
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $mMessage = new Models\Message($this->main);
      $mMessage->dropTableIfExists()->install();
    }
  }

  public function getNotificationsCount(): int
  {
    $mMessage = new \HubletoApp\Community\Messages\Models\Message($this->main);
    return $mMessage->record->prepareReadQuery()
      ->where('id_owner', $this->main->auth->getUserId())
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
  ): void
  {
    $user = $this->main->auth->getUser();
    $fromEmail = $user['email'] ?? '';

    $mUser = new \HubletoApp\Community\Settings\Models\User($this->main);
    $users = $mUser->record->get()->toArray();
    $usersByEmail = [];
    $emailsByUserId = [];
    foreach ($users as $user) {
      $usersByEmail[$user['email']] = $user['id'];
      $emailsByUserId[$user['id']] = $user['email'];
    }

    if (!empty($fromEmail)) {
      if (is_string($to)) $toEmails = $this->parseEmailsFromString($to);
      else $toEmails = [ $emailsByUserId[$to] ];

      if (is_string($cc)) $ccEmails = $this->parseEmailsFromString($cc);
      else $ccEmails = [ $emailsByUserId[$cc] ];

      if (is_string($bcc)) $bccEmails = $this->parseEmailsFromString($bcc);
      else $bccEmails = [ $emailsByUserId[$bcc] ];

      $mMessage = new \HubletoApp\Community\Messages\Models\Message($this->main);

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

      foreach ($toEmails as $email) {
        $tmpIdOwner = $usersByEmail[$email] ?? 0;
        if ($tmpIdOwner > 0) {
          $mMessage->record->recordCreate(array_merge($messageData, [
            'id_owner' => $tmpIdOwner,
          ]));
        }
      }
    }
  }
}
