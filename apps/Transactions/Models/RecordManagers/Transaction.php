<?php

namespace Hubleto\App\Community\Transactions\Models\RecordManagers;

use Hubleto\App\Community\Customers\Models\RecordManagers\Customer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

class Transaction extends \Hubleto\Erp\RecordManager
{
  public $table = 'transactions_transaction';

  /** @return BelongsTo<AccountType, covariant Transaction> */
//  public function ACCOUNT_TYPE(): BelongsTo
//  {
//    return $this->belongsTo(AccountType::class, 'id_account_type', 'id');
//  }

  /** @return BelongsTo<AccountSubtype, covariant Transaction> */
//  public function ACCOUNT_SUBTYPE(): BelongsTo
//  {
//    return $this->belongsTo(AccountSubtype::class, 'id_account_subtype', 'id');
//  }
}
