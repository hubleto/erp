<?php

namespace HubletoApp\Community\Mail\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use HubletoApp\Community\Settings\Models\RecordManagers\User;

class Mailbox extends \HubletoMain\RecordManager
{
  public $table = 'mails_mailboxes';

  /** @return BelongsTo<User, covariant Customer> */
  public function ACCOUNT(): BelongsTo {
    return $this->belongsTo(Account::class, 'id_account', 'id');
  }

}
