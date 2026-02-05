<?php

namespace Hubleto\App\Community\Mail;

use Hubleto\App\Community\Mail\Models\Account;
use Hubleto\App\Community\Mail\Models\Mail;
use Hubleto\App\Community\Mail\Models\Mailbox;
use Ddeboer\Imap\Server;
use Hubleto\Framework\Helper;

class Mailer extends \Hubleto\Erp\Core
{

  public function compileEmailAddresses(array $input): string
  {
    $addresses = [];

    foreach ($input as $item) {
      $addresses[] = $item->mailbox . '@' . $item->host;
    }

    return join(', ', $addresses);
  }

  /**
   * [Description for getMails]
   *
   * @return array<string> Log of the operation
   * 
   */
  public function getMails(): array
  {
    $result = ['log' => []];

    try {

      $today = new \DateTimeImmutable();
      $sixtyDaysAgo = $today->sub(new \DateInterval('P60D'));

      /** @var Account */
      $mAccount = $this->getModel(Account::class);
      $accounts = $mAccount->record->get()->toArray();

      /** @var Mailbox */
      $mMailbox = $this->getModel(Mailbox::class);
      $mMail = $this->getModel(Mail::class);

      $this->logger()->info('GetMails: getting emails from ' . count($accounts) . ' account(s).');
      $result['log'][] = 'GetMails: getting emails from ' . count($accounts) . ' account(s).';

      foreach ($accounts as $account) {
        $this->logger()->info('GetMails: checking account ' . $account['name']);
        $result['log'][] = 'GetMails: checking account ' . $account['name'];
        $localMailboxes = Helper::keyBy('name', $mMailbox->record->where('id_account', $account['id'])->get()->toArray());

        if (empty($account['imap_host'])) continue;
        if (empty($account['imap_port'])) continue;
        if (empty($account['imap_encryption'])) continue;
        if (empty($account['imap_username'])) continue;
        if (empty($account['imap_password'])) continue;

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
          $result['log'][] = 'GetMails: mailbox ' . $account['name'] . ' -> ' . $imapMailbox->getName();

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
              'attributes' => $imapMailbox->getAttributes(),
            ];

            $localMailbox['id'] = $mMailbox->record->recordCreate($localMailbox)['id'];
          }

          $messages = $imapMailbox->getMessages(
            new \Ddeboer\Imap\Search\Date\Since($sixtyDaysAgo),
            \SORTDATE, // Sort criteria
            true // Descending order
          );

          $mailsInMailbox = $mMail->record
            ->select('mail_id', 'mail_number')
            ->where('id_account', $account['id'])
            ->where('id_mailbox', $localMailbox['id'])
            ->get()->toArray()
          ;

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
            $result['log'][] = 'GetMails: found mail ' . $mailNumber . ' ' . $mailId;

            if (
              !in_array($mailNumber, $mailNumbers)
              && !in_array($mailId, $mailIds)
            ) {
              $this->logger()->info('GetMails: creating mail in database');
              $result['log'][] = 'GetMails: creating mail in database';
              $mMail->record->recordCreate([
                'id_account' => $account['id'],
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
                'datetime_created' => date("Y-m-d H:i:s"),
                'datetime_sent' => $message->getDate()->format("Y-m-d H:i:s"),
              ]);
            }
          }
        }
      }
    } catch (\Throwable $e) {
      $result['log'][] = get_class($e);
      $result['log'][] = $e->getMessage();
      $result['log'][] = $e->getTraceAsString();
    }

    return $result;

  }

}
