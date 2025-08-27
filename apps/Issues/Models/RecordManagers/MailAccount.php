<?php

namespace HubletoApp\Community\Issues\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use HubletoApp\Community\Mail\Models\Account;

class MailAccount extends \HubletoMain\RecordManager
{
  public $table = 'issues_mail_accounts';

  /** @return BelongsTo<Customer, covariant BillingAccount> */
  public function CUSTOMER(): BelongsTo
  {
    return $this->belongsTo(Account::class, 'id_main_account', 'id');
  }

}
