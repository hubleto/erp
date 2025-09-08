<?php

namespace Hubleto\App\Community\Mail\Crons;

use Hubleto\App\Community\Mail\Models\Account;
use Hubleto\App\Community\Mail\Models\Mail;
use Hubleto\App\Community\Mail\Models\Mailbox;
use Ddeboer\Imap\Server;
use Hubleto\Framework\Helper;

class GetMails extends \Hubleto\Erp\Cron
{
  public string $schedulingPattern = '* * * * *';

  public function compileEmailAddresses(array $input): string
  {
    $addresses = [];

    foreach ($input as $item) {
      $addresses[] = $item->mailbox . '@' . $item->host;
    }

    return join(', ', $addresses);
  }

  public function run(): void
  {
    $today = new \DateTimeImmutable();
    $thirtyDaysAgo = $today->sub(new \DateInterval('P30D'));

    /** @var Account */
    $mAccount = $this->getModel(Account::class);
    $accounts = $mAccount->record->get()->toArray();

    /** @var Mailbox */
    $mMailbox = $this->getModel(Mailbox::class);
    $mMail = $this->getModel(Mail::class);

    foreach ($accounts as $account) {
      $this->logger()->info('GetMails: checking account ' . $account['name']);
      $localMailboxes = Helper::keyBy('name', $mMailbox->record->where('id_account', $account['id'])->get()->toArray());

      $server = new Server(
        $account['imap_host'],
        $account['imap_port'],
        $account['imap_encryption'], 
      );
      $connection = $server->authenticate(
        $account['imap_username'],
        $mAccount->decryptPassword($account['imap_password'])
      );

      $imapMailboxes = $connection->getMailboxes();

      foreach ($imapMailboxes as $imapMailbox) {
        $this->logger()->info('GetMails: mailbox ' . $account['name'] . ' -> ' . $imapMailbox->getName());

        // Skip container-only mailboxes
        // @see https://secure.php.net/manual/en/function.imap-getmailboxes.php
        if ($imapMailbox->getAttributes() & \LATT_NOSELECT) {
            continue;
        }

        if (isset($localMailboxes[$imapMailbox->getName()])) {
            $localMailbox = $localMailboxes[$imapMailbox->getName()];
        } else {
          $localMailbox = [
            'id_account' => $account['id'],
            'name' => $imapMailbox->getName(),
          ];

          $localMailbox['id'] = $mMailbox->record->recordCreate($localMailbox)['id'];
        }

        $messages = $imapMailbox->getMessages(
          new \Ddeboer\Imap\Search\Date\Since($thirtyDaysAgo),
          \SORTDATE, // Sort criteria
          true // Descending order
        );

        $mailsInMailbox = $mMail->record->select('mail_id', 'mail_number')->where('id_mailbox', $localMailbox['id'])->get()->toArray();

        $mailIds = [];
        $mailNumbers = [];
        foreach ($mailsInMailbox as $mail) {
          $mailIds[] = $mail['mail_id'];
          $mailNumbers[] = $mail['mail_number'];
        }

        foreach ($messages as $message) {
          $mailNumber = $message->getNumber();
          $mailId = $message->getId();
          $mailHeaders = $message->getHeaders();

          $this->logger()->info('GetMails: found mail ' . $mailNumber . ' ' . $mailId);

          if (
            !in_array($mailNumber, $mailNumbers)
            && !in_array($mailId, $mailIds)
          ) {
            $this->logger()->info('GetMails: creating mail in database');
            $mMail->record->recordCreate([
              'id_mailbox' => $localMailbox['id'],
              'mail_id' => $mailId,
              'mail_number' => $mailNumber,
              'mail_folder' => 'INBOX',
              'subject' => $message->getSubject(),
              'from' => $this->compileEmailAddresses($mailHeaders['from']),
              'to' => $this->compileEmailAddresses($mailHeaders['to']),
              // 'cc' => $this->compileEmailAddresses($mailHeaders['cc']),
              // 'sent' => $this->compileEmailAddresses($mailHeaders['cc']),
              'body_text' => $message->getBodyText(),
              'body_html' => $message->getBodyHtml(),
            ]);
          }
        }
      }
    }

  }

}
