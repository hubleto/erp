<?php

namespace HubletoApp\Community\Orders\Models\Eloquent;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class History extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'order_histories';

  public function ORDER(): BelongsTo {
    return $this->belongsTo(Order::class, 'id_order', 'id');
  }
}