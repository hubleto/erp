<?php

namespace Hubleto\App\Community\Mail\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mailbox extends \Hubleto\Erp\RecordManager
{
  public $table = 'mails_mailboxes';

  /** @return BelongsTo<User, covariant Customer> */
  public function ACCOUNT(): BelongsTo {
    return $this->belongsTo(Account::class, 'id_account', 'id');
  }

}
