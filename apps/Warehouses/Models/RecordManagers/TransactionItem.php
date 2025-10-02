<?php

namespace Hubleto\App\Community\Warehouses\Models\RecordManagers;


use Hubleto\App\Community\Products\Models\RecordManagers\Product;
use Hubleto\App\Community\Warehouses\Models\RecordManagers\Location;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Hubleto\App\Community\Auth\Models\RecordManagers\User;

class TransactionItem extends \Hubleto\Erp\RecordManager
{
  public $table = 'warehouses_transactions_items';

  public function TRANSACTION(): BelongsTo
  {
    return $this->belongsTo(Transaction::class, 'id_transaction', 'id');
  }

  public function PRODUCT(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'id_product', 'id');
  }

  public function USER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_user', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $idTransaction = $hubleto->router()->urlParamAsInteger("idTransaction");
    $idProduct = $hubleto->router()->urlParamAsInteger("idProduct");

    if ($idTransaction > 0) $query = $query->where($this->table . '.id_transaction', $idTransaction);
    if ($idProduct > 0) $query = $query->where($this->table . '.id_product', $idProduct);

    return $query;
  }

}
