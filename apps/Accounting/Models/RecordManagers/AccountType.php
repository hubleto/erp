<?php

namespace Hubleto\App\Community\Accounting\Models\RecordManagers;

use Hubleto\App\Community\Customers\Models\RecordManagers\Customer;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountType extends \Hubleto\Erp\RecordManager
{
  public $table = 'accounting_account_type';

  /** @return HasMany<Entry> */
  public function ACCOUNT(): hasMany
  {
    return $this->hasMany(Account::class, 'id_account_type', 'id');
  }
}
