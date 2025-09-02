<?php

namespace Hubleto\App\Community\Orders\Models\RecordManagers;

use Hubleto\App\Community\Products\Models\RecordManagers\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderProduct extends \Hubleto\Erp\RecordManager
{
  public $table = 'orders_products';

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


  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \Hubleto\Erp\Loader::getGlobalApp();

    if ($main->router()->urlParamAsInteger("idOrder") > 0) {
      $query = $query->where($this->table . '.id_order', $main->router()->urlParamAsInteger("idOrder"));
    }

    return $query;
  }
}
