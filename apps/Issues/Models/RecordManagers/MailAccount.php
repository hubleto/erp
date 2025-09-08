<?php

namespace Hubleto\App\Community\Issues\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Hubleto\App\Community\Mail\Models\Account;

class MailAccount extends \Hubleto\Erp\RecordManager
{
  public $table = 'issues_mail_accounts';

  /** @return BelongsTo<Customer, covariant BillingAccount> */
  public function CUSTOMER(): BelongsTo
  {
    return $this->belongsTo(Account::class, 'id_main_account', 'id');
  }

}
