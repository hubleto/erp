<?php

namespace Hubleto\App\Community\Mail\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends \Hubleto\Erp\RecordManager
{
  public $table = 'mails_accounts';


  /** @return HasMany<DealTask, covariant Deal> */
  public function MAILBOXES(): HasMany
  {
    return $this->hasMany(Mailbox::class, 'id_account', 'id');
  }
}
