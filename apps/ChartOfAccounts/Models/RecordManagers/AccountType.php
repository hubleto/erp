<?php

namespace Hubleto\App\Community\ChartOfAccounts\Models\RecordManagers;

use Hubleto\App\Community\ChartOfAccounts\Models\JournalEntry;
use Hubleto\App\Community\Customers\Models\RecordManagers\Customer;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountType extends \Hubleto\Erp\RecordManager
{
  public $table = 'accounting_account_type';

  /** @return HasMany<JournalEntry> */
  public function CUSTOMER(): hasMany
  {
    return $this->hasMany(JournalEntry::class, 'id_account_type', 'id');
  }
}
