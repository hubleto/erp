<?php

namespace Hubleto\App\Community\Mail;

use Hubleto\Erp\Core;

class Counter extends Core
{

  /**
   * [Description for dueItemsNotPreparedForInvoice]
   *
   * @return int
   * 
   */
  public function alUnreadMails(): int
  {
    /** @var Models\Account */
    $mAccount = $this->getModel(Models\Account::class);

    /** @var Models\Mail */
    $mMail = $this->getModel(Models\Mail::class);
    $accounts = $mAccount->record->prepareReadQuery()->with('MAILBOXES')->get();

    $idMailboxes = [];

    foreach ($accounts as $account) {
      if (
        !empty($account->imap_host)
        && !empty($account->imap_port)
        && !empty($account->imap_encryption)
        && !empty($account->imap_username)
        && !empty($account->imap_password)
      ) {
        foreach ($account->MAILBOXES as $mailbox) {
          $idMailboxes[] = $mailbox->id;
        }
      }
    }

    return $mMail->record
      ->whereNull('datetime_read')
      ->whereIn('mails.id_mailbox', $idMailboxes)
      ->count()
    ;
  }

}
