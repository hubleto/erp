<?php

namespace Hubleto\App\Community\Suppliers\Models\RecordManagers;

use Hubleto\App\Community\Settings\Models\RecordManagers\Country;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Supplier extends \Hubleto\Erp\RecordManager
{
  public $table = 'suppliers';

  /** @return BelongsTo<AccountType, covariant Account> */
  public function COUNTRY(): BelongsTo
  {
    return $this->belongsTo(Country::class, 'id_country', 'id');
  }

}
