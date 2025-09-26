<?php

namespace Hubleto\App\Community\BkChartOfAccounts\Models\RecordManagers;

use Hubleto\App\Community\BkChartOfAccounts\Models\JournalEntry;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountSubtype extends \Hubleto\Erp\RecordManager
{
  public $table = 'accounting_account_subtype';

  /** @return HasMany<JournalEntry> */
  public function ACCOUNT(): hasMany
  {
    return $this->hasMany(Account::class, 'id_account_subtype', 'id');
  }
}
