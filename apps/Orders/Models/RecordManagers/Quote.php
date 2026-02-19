<?php

namespace Hubleto\App\Community\Orders\Models\RecordManagers;

use Hubleto\App\Community\Invoices\Models\RecordManagers\Item;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Hubleto\App\Community\Auth\Models\RecordManagers\User;

class Quote extends \Hubleto\Erp\RecordManager
{
  public $table = 'orders_quotes';

  /** @return BelongsTo<User, covariant User> */
  public function ORDER(): BelongsTo
  {
    return $this->belongsTo(Order::class, 'id_order', 'id');
  }

  /** @return BelongsTo<User, covariant Lead> */
  public function APPROVED_BY(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_approved_by', 'id');
  }

  /** @return BelongsTo<User, covariant Lead> */
  public function OWNER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_owner', 'id');
  }

  /** @return BelongsTo<User, covariant Lead> */
  public function MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_manager', 'id');
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

    if ($idOrder > 0) $query = $query->where($this->table . '.id_order', $idOrder);

    return $query;
  }

}
