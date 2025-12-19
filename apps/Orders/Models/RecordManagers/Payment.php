<?php

namespace Hubleto\App\Community\Orders\Models\RecordManagers;

use Hubleto\App\Community\Invoices\Models\RecordManagers\Item;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends \Hubleto\Erp\RecordManager
{
  public $table = 'orders_payments';

  /** @return BelongsTo<User, covariant User> */
  public function ORDER(): BelongsTo
  {
    return $this->belongsTo(Order::class, 'id_order', 'id');
  }

  /** @return BelongsTo<User, covariant User> */
  public function INVOICE_ITEM(): BelongsTo
  {
    return $this->belongsTo(Item::class, 'id_invoice_item', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $idOrder = $hubleto->router()->urlParamAsInteger("idOrder");

    if ($idOrder > 0) $query = $query->where($this->table . '.id_order', $idOrder);

    $filters = $hubleto->router()->urlParamAsArray("filters");
    if (isset($filters["fStatus"])) {
      if ($filters["fStatus"] == 1) {
        $query = $query->whereNull($this->table . ".id_invoice_item");
      } else {
        $query = $query->where($this->table . ".id_invoice_item", ">", 0);
      }
    }

    return $query;
  }

}
