<?php

namespace Hubleto\App\Community\Cashdesk\Models\RecordManagers;

use Hubleto\App\Community\Settings\Models\RecordManagers\Company;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashRegister extends \Hubleto\Erp\RecordManager
{
  public $table = 'cashdesk_cash_registers';

  public function COMPANY(): BelongsTo
  {
    return $this->belongsTo(Company::class, 'id_company', 'id');
  }

}
