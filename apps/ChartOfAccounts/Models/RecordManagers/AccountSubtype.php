<?php

namespace Hubleto\App\Community\ChartOfAccounts\Models\RecordManagers;

use Hubleto\App\Community\ChartOfAccounts\Models\JournalEntry;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountSubtype extends \Hubleto\Erp\RecordManager
{
  public $table = 'accounting_account_subtype';

  /** @return HasMany<JournalEntry> */
  public function CUSTOMER(): hasMany
  {
    return $this->hasMany(Account::class, 'id_account_subtype', 'id');
  }
}
