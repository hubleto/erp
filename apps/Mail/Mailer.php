<?php

namespace Hubleto\App\Community\Mail;

use Hubleto\App\Community\Mail\Models\Account;
use Hubleto\App\Community\Mail\Models\Mail;
use Hubleto\App\Community\Mail\Models\Mailbox;
use Ddeboer\Imap\Server;
use Hubleto\App\Community\Mail\Models\Attachment;
use Hubleto\Framework\Helper;

class Mailer extends \Hubleto\Erp\Core
{
  public string $translationContext = 'hubleto-app-community-mail-loader';
  public string $translationContextInner = 'Mailer';


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

      /** @var Mail */
      $mMail = $this->getModel(Mail::class);

      /** @var Attachment */
      $mAttachment = $this->getModel(Attachment::class);

      $this->logger()->info('GetMails: getting emails from ' . count($accounts) . ' account(s).');
      $result['log'][] = $this->translate('Getting emails from {{ count }} account(s).', ['count' => count($accounts)]);

      foreach ($accounts as $account) {
        $this->logger()->info('GetMails: checking account ' . $account['name']);
        $result['log'][] = $this->translate('Checking account {{ name }}', ['name' => $account['name']]);
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
          $result['log'][] = $this->translate('Mailbox {{ account }} -> {{ mailbox }}', ['account' => $account['name'], 'mailbox' => $imapMailbox->getName()]);

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
            $result['log'][] = $this->translate('Found mail {{ number }} {{ id }}', ['number' => $mailNumber, 'id' => $mailId]);

            if (
              !in_array($mailNumber, $mailNumbers)
              && !in_array($mailId, $mailIds)
            ) {
              $this->logger()->info('GetMails: creating mail in database');
              $result['log'][] = $this->translate('Creating mail in database');

              $idMail = $mMail->record->recordCreate([
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
              ])['id'];

              $maxAttachmentSize = ($account['max_attachment_size'] ?? 0) * 1024 * 1024;

              if ($maxAttachmentSize > 0) {
                $attachments = $message->getAttachments();

                foreach ($attachments as $attachment) {
                  // $attachment is instance of \Ddeboer\Imap\Message\Attachment

                  $tmp = pathinfo($attachment->getFilename());
                  $attachmentFilename = 
                    $message->getDate()->format("Ymd-His")
                    . '-'
                    . Helper::str2url($tmp['filename'])
                    . '.' . $tmp['extension']
                  ;

                  if (($attachment->getSize() ?? 0) > $maxAttachmentSize) {
                    file_put_contents(
                      $this->env()->uploadFolder . '/' . $attachmentFilename,
                      'Attachment size exceeded maximum limit (' . $account['max_attachment_size'] . ' MB)'
                    );
                  } else {
                    file_put_contents(
                      $this->env()->uploadFolder . '/' . $attachmentFilename,
                      $attachment->getDecodedContent()
                    );
                  }

                  $mAttachment->record->recordCreate([
                    'id_mail' => $idMail,
                    'name' => $attachment->getFilename(),
                    'size' => $attachment->getSize(),
                    'file' => $attachmentFilename,
                  ]);
                }
              }
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
