<?php

namespace HubletoApp\Community\Orders\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class History extends \HubletoMain\RecordManager
{
  public $table = 'order_histories';

  /** @return BelongsTo<Order, covariant History> */
  public function ORDER(): BelongsTo
  {
    return $this->belongsTo(Order::class, 'id_order', 'id');
  }

}
