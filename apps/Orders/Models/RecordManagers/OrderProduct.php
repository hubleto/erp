<?php

namespace HubletoApp\Community\Orders\Models\RecordManagers;

use HubletoApp\Community\Products\Models\RecordManagers\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderProduct extends \Hubleto\Framework\RecordManager
{
  public $table = 'order_products';

  /** @return BelongsTo<Order, covariant OrderProduct> */
  public function ORDER(): BelongsTo
  {
    return $this->belongsTo(Order::class, 'id_order', 'id');
  }

  /** @return BelongsTo<Product, covariant OrderProduct> */
  public function PRODUCT(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'id_product', 'id');
  }
}
