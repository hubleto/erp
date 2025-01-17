<?php

namespace HubletoApp\Community\Shop\Models\Eloquent;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'orders';

  public function PRODUCTS(): HasMany {
    return $this->hasMany(OrderProduct::class, 'id_order', 'id');
  }
}