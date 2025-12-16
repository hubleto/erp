<?php

namespace Hubleto\App\Community\Orders\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends \Hubleto\Erp\RecordManager
{
  public $table = 'orders_payments';

  /** @return BelongsTo<User, covariant User> */
  public function ORDER(): BelongsTo
  {
    return $this->belongsTo(Order::class, 'id_order', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $idOrder = $hubleto->router()->urlParamAsInteger("idOrder");

    if ($idOrder > 0) $query = $query->where($this->table . '.id_order', $idOrder);

    return $query;
  }

}
