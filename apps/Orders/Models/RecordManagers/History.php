<?php

namespace Hubleto\App\Community\Orders\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class History extends \Hubleto\Erp\RecordManager
{
  public $table = 'order_histories';

  /** @return BelongsTo<Order, covariant History> */
  public function ORDER(): BelongsTo
  {
    return $this->belongsTo(Order::class, 'id_order', 'id');
  }

  /**
   * [Description for prepareReadQuery]
   *
   * @param mixed|null $query
   * @param int $level
   * @param array|null|null $includeRelations
   * 
   * @return mixed
   * 
   */
  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $idOrder = $hubleto->router()->urlParamAsInteger("idOrder");

    if ($idOrder > 0) {
      $query = $query->where($this->table . '.id_order', $idOrder);
    }

    return $query;
  }

}
