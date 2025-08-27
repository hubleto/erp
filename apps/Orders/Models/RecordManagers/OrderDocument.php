<?php

namespace HubletoApp\Community\Orders\Models\RecordManagers;

use HubletoApp\Community\Documents\Models\RecordManagers\Document;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDocument extends \HubletoMain\RecordManager
{
  public $table = 'orders_documents';

  /** @return BelongsTo<Order, covariant OrderProduct> */
  public function ORDER(): BelongsTo
  {
    return $this->belongsTo(Order::class, 'id_order', 'id');
  }

  /** @return BelongsTo<Product, covariant OrderProduct> */
  public function DOCUMENT(): BelongsTo
  {
    return $this->belongsTo(Document::class, 'id_document', 'id');
  }
}
