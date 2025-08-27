<?php

namespace HubletoApp\Community\Orders\Models\RecordManagers;

use HubletoApp\Community\Deals\Models\RecordManagers\Deal;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDeal extends \HubletoMain\RecordManager
{
  public $table = 'orders_deals';

  /** @return BelongsTo<Order, covariant OrderProduct> */
  public function ORDER(): BelongsTo
  {
    return $this->belongsTo(Order::class, 'id_order', 'id');
  }

  /** @return BelongsTo<Product, covariant OrderProduct> */
  public function DEAL(): BelongsTo
  {
    return $this->belongsTo(Deal::class, 'id_deal', 'id');
  }
}
