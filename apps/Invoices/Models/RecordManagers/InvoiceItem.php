<?php

namespace HubletoApp\Community\Invoices\Models\RecordManagers;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends \HubletoMain\RecordManager {
  public $table = 'invoice_items';

  /** @return BelongsTo<Invoice, covariant InvoiceItem> */
  public function INVOICE(): BelongsTo {
    return $this->BelongsTo(Invoice::class, 'id_invoice');
  }

}
