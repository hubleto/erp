<?php

namespace Hubleto\App\Community\Mail;

use Hubleto\App\Community\Mail\Models\Account;

class Loader extends \Hubleto\Framework\App
{
  public array $templateVariables = [];

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
      '/^mail\/?(?<idMailbox>\d+)?\/?$/' => Controllers\Home::class,

      '/^mail\/accounts\/?(?<idAccount>\d+)?\/?$/' => Controllers\Accounts::class,
      '/^mail\/accounts\/add\/?$/' => ['controller' => Controllers\Accounts::class, 'vars' => ['recordId' => -1]],

      '/^mail\/scheduled\/?$/' => Controllers\Scheduled::class,
      '/^mail\/sent\/?$/' => Controllers\Sent::class,
      '/^mail\/get\/?$/' => Controllers\Get::class,
      '/^mail\/mailboxes\/?$/' => Controllers\Mailboxes::class,
      '/^mail\/mails\/(?<idMailbox>\d+)\/?$/' => Controllers\Mails::class,
      '/^mail\/mails\/add\/?$/' => ['controller' => Controllers\Mails::class, 'vars' => ['recordId' => -1]],
      // '/^mail\/drafts\/?$/' => Controllers\Drafts::class,

      '/^mail\/templates\/?(?<recordId>\d+)?\/?$/' => Controllers\Templates::class,
      '/^mail\/templates\/add\/?$/' => ['controller' => Controllers\Templates::class, 'vars' => ['recordId' => -1]],

      '/^mail\/api\/mark-as-read\/?$/' => Controllers\Api\MarkAsRead::class,
      '/^mail\/api\/mark-as-unread\/?$/' => Controllers\Api\MarkAsUnread::class,
    ]);

    $this->cronManager()->addCron(Crons\GetMails::class);
    $this->cronManager()->addCron(Crons\SendMails::class);

    $this->templateVariables = $this->collectExtendibles('MailTemplateVariables');
    $this->sidebarView = '@Hubleto:App:Community:Mail/Sidebar.twig';
  }

  /**
   * [Description for renderSecondSidebar]
   *
   * @return string
   * 
   */
  public function renderSecondSidebar(): string
  {
    $activeIdMailbox = $this->router()->urlParamAsInteger('idMailbox');

    $mAccount = $this->getModel(Account::class);
    $accounts = $mAccount->record->prepareReadQuery()->with('MAILBOXES')->get();

    $accountsHtml = '';
    foreach ($accounts as $account) {
      $accountsHtml .= '
        <div class="bg-primary/10 p-1 text-sm"> ' . $account->name . '</div>
        <div class="list">
      ';

      if (count($account->MAILBOXES) == 0) {
        $accountsHtml .= '
          <div class="alert alert-info text-sm m-2">Account has no mailboxes.</div>
        ';
      } else {
        foreach ($account->MAILBOXES as $mailbox) {
          $accountsHtml .= '
            <a
              class="
                btn btn-small btn-list-item
                ' . ($mailbox->id == $activeIdMailbox ? "btn-primary" : "btn-transparent") . '
              "
              href="' . $this->env()->projectUrl . '/mail/' . $mailbox->id . '"
            >
              <span class="text">' . $mailbox->name . '</span>
            </a>
          ';
        }
      }
        
      $accountsHtml .= '
        </div>
      ';
    }

    return '
      ' . $accountsHtml . '

      <div>
        <a
          class="btn btn-small btn-list-item btn-transparent mt-2"
          href="' . $this->env()->projectUrl . '/mail/get"
        >
          <span class="icon"><i class="fas fa-download"></i></span>
          <span class="text">Get emails</span>
        </a>
        <a
          class="btn btn-small btn-list-item btn-transparent mt-2"
          href="' . $this->env()->projectUrl . '/mail/accounts"
        >
          <span class="icon"><i class="fas fa-download"></i></span>
          <span class="text">Manage accounts</span>
        </a>
      </div>

    ';
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Account::class)->dropTableIfExists()->install();
      $this->getModel(Models\Mailbox::class)->dropTableIfExists()->install();
      $this->getModel(Models\Mail::class)->dropTableIfExists()->install();
      $this->getModel(Models\Template::class)->dropTableIfExists()->install();
      $this->getModel(Models\Index::class)->dropTableIfExists()->install();
    }
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

  public function getCipherKey(): string
  {
    $key = $this->configAsString('cipherKey');
    if (empty($key)) {
      $this->saveConfig('cipherKey', openssl_random_pseudo_bytes(64));
      $key = $this->configAsString('cipherKey');
    }
    return $key;
  }

  public function send(
    int|string $to,
    int|string $cc,
    int|string $bcc,
    string $subject,
    string $body,
    string $color = '',
    int $priority = 0
  ): array {
    $user = $this->getService(\Hubleto\Framework\AuthProvider::class)->getUser();
    $idUser = $user['id'] ?? 0;
    $fromEmail = $user['email'] ?? '';

    $mUser = $this->getModel(\Hubleto\App\Community\Auth\Models\User::class);
    $users = $mUser->record->get()->toArray();
    $usersByEmail = [];
    $emailsByUserId = [];
    foreach ($users as $user) {
      $usersByEmail[$user['email']] = $user['id'];
      $emailsByUserId[$user['id']] = $user['email'];
    }

    $mail = [];

    if (!empty($fromEmail)) {

      if (is_string($to)) {
        $toEmails = $this->parseEmailsFromString($to);
      } else {
        $toEmails = [ $emailsByUserId[$to] ];
      }

      if (is_string($cc)) {
        $ccEmails = $this->parseEmailsFromString($cc);
      } else {
        $ccEmails = [ $emailsByUserId[$cc] ];
      }

      if (is_string($bcc)) {
        $bccEmails = $this->parseEmailsFromString($bcc);
      } else {
        $bccEmails = [ $emailsByUserId[$bcc] ];
      }

      $mMail = $this->getModel(Models\Mail::class);
      $mIndex = $this->getModel(Models\Index::class);

      $mailData = [
        'priority' => $priority,
        'sent' => date('Y-m-d H:i:s'),
        'from' => $fromEmail,
        'to' => join(', ', $toEmails),
        'cc' => join(', ', $ccEmails),
        'subject' => $subject,
        'body' => $body,
        'color' => $color,
      ];

      $mail = $mMail->record->create($mailData)->toArray();
      $idMail = $mail['id'] ?? 0;

      if ($idMail > 0) {
        foreach ($toEmails as $email) {
          $idUserTo = $usersByEmail[$email] ?? 0;
          if ($idUserTo > 0) {
            $mIndex->record->create(['id_mail' => $idMail, 'id_from' => $idUser, 'id_to' => $idUserTo]);
          }
        }
        foreach ($ccEmails as $email) {
          $idUserCc = $usersByEmail[$email] ?? 0;
          if ($idUserCc > 0) {
            $mIndex->record->create(['id_mail' => $idMail, 'id_from' => $idUser, 'id_cc' => $idUserCc]);
          }
        }
        foreach ($bccEmails as $email) {
          $idUserBcc = $usersByEmail[$email] ?? 0;
          if ($idUserBcc > 0) {
            $mIndex->record->create(['id_mail' => $idMail, 'id_from' => $idUser, 'id_bcc' => $idUserBcc]);
          }
        }
      }
    }

    return $mail;
  }

}
