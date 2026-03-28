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
    $mMail = $this->getModel(Models\Mail::class);
    return $mMail->record
      ->whereNull('datetime_read')
      ->where('mails.id_mailbox', '>', 0)
      ->count()
    ;
  }

}
