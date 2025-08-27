<?php

namespace HubletoApp\Community\Orders\Models\RecordManagers;

use HubletoApp\Community\Invoices\Models\RecordManagers\Invoice;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderInvoice extends \HubletoMain\RecordManager
{
  public $table = 'orders_invoices';

  /** @return BelongsTo<Order, covariant OrderProduct> */
  public function ORDER(): BelongsTo
  {
    return $this->belongsTo(Order::class, 'id_order', 'id');
  }

  /** @return BelongsTo<Product, covariant OrderProduct> */
  public function INVOICE(): BelongsTo
  {
    return $this->belongsTo(Invoice::class, 'id_invoice', 'id');
  }
}
