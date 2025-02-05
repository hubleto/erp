<?php

namespace HubletoApp\Community\Orders\Models\Eloquent;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class History extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'order_histories';

  /** @return BelongsTo<Order, covariant History> */
  public function ORDER(): BelongsTo {
    return $this->belongsTo(Order::class, 'id_order', 'id');
  }

}