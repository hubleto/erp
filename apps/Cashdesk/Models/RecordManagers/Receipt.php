<?php

namespace Hubleto\App\Community\Cashdesk\Models\RecordManagers;

use Hubleto\App\Community\Settings\Models\RecordManagers\Company;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Receipt extends \Hubleto\Erp\RecordManager
{
  public $table = 'cashdesk_receipts';

  public function COMPANY(): BelongsTo
  {
    return $this->belongsTo(Company::class, 'id_company', 'id');
  }

  public function CASH_REGISTER(): BelongsTo
  {
    return $this->belongsTo(CashRegister::class, 'id_cash_register', 'id');
  }

  public function ITEMS(): HasMany
  {
    return $this->hasMany(ReceiptItem::class, 'id_receipt', 'id' );
  }

}
