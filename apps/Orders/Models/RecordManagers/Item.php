<?php

namespace Hubleto\App\Community\Orders\Models\RecordManagers;

use Hubleto\App\Community\Products\Models\RecordManagers\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Item extends \Hubleto\Erp\RecordManager
{
  public $table = 'orders_items';

  /** @return BelongsTo<Order, covariant OrderItem> */
  public function ORDER(): BelongsTo
  {
    return $this->belongsTo(Order::class, 'id_order', 'id');
  }

  /** @return BelongsTo<Item, covariant Item> */
  public function PRODUCT(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'id_product', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $idOrder = $hubleto->router()->urlParamAsInteger("idOrder");

    if ($idOrder > 0) $query = $query->where($this->table . '.id_order', $idOrder);

    return $query;
  }

  public function prepareLookupQuery(string $search): mixed
  {
    $query = parent::prepareLookupQuery($search);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $idOrder = $hubleto->router()->urlParamAsInteger("idOrder");

    if ($idOrder > 0) $query = $query->where($this->table . '.id_order', $idOrder);
    return $query;
  }
}
