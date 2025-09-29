<?php

namespace Hubleto\App\Community\Accounting\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountSubtype extends \Hubleto\Erp\RecordManager
{
  public $table = 'accounting_account_subtype';

  /** @return HasMany<Entry> */
  public function ACCOUNT(): hasMany
  {
    return $this->hasMany(Account::class, 'id_account_subtype', 'id');
  }
}
