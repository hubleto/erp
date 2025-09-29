<?php

namespace Hubleto\App\Community\Accounting\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends \Hubleto\Erp\RecordManager
{
  public $table = 'accounting_transaction';

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
