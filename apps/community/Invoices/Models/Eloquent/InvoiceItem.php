<?php

namespace HubletoApp\Community\Invoices\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends \HubletoMain\Core\ModelEloquent {
  public $table = 'invoice_items';

  public function INVOICE(): BelongsTo { return $this->BelongsTo(Invoice::class, 'id_invoice'); }

}
