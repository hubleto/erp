<?php

namespace Hubleto\App\Community\ChartOfAccounts\Models\RecordManagers;

use Hubleto\App\Community\Billing\Models\RecordManagers\BillingAccountService;
use Hubleto\App\Community\Customers\Models\RecordManagers\Customer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends \Hubleto\Erp\RecordManager
{
  public $table = 'accounting_account';

  /** @return BelongsTo<AccountType, covariant Account> */
  public function ACCOUNT_TYPE(): BelongsTo
  {
    return $this->belongsTo(AccountType::class, 'id_account_type', 'id');
  }

  /** @return BelongsTo<AccountSubtype, covariant Account> */
  public function ACCOUNT_SUBTYPE(): BelongsTo
  {
    return $this->belongsTo(AccountSubtype::class, 'id_account_subtype', 'id');
  }
}
