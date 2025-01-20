<?php

namespace HubletoApp\Community\Shop\Models\Eloquent;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderProduct extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'order_products';

  public function ORDER(): BelongsTo {
    return $this->belongsTo(Order::class, 'id_order', 'id');
  }
  public function PRODUCT(): BelongsTo {
    return $this->belongsTo(Product::class, 'id_product', 'id');
  }
}