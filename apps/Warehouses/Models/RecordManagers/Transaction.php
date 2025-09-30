<?php

namespace Hubleto\App\Community\Warehouses\Models\RecordManagers;


use Hubleto\App\Community\Products\Models\RecordManagers\Product;
use Hubleto\App\Community\Warehouses\Models\RecordManagers\Location;
use Hubleto\App\Community\Auth\Models\RecordManagers\User;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends \Hubleto\Erp\RecordManager
{
  public $table = 'warehouses_transactions';

  public function PRODUCT(): BelongsTo
  {
    return $this->belongsTo(Product::class, 'id_product', 'id');
  }

  public function LOCATION_SOURCE(): BelongsTo
  {
    return $this->belongsTo(Location::class, 'id_location_source', 'id');
  }

  public function LOCATION_DESTINATION(): BelongsTo
  {
    return $this->belongsTo(Location::class, 'id_location_destination', 'id');
  }

  public function USER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_user', 'id');
  }

  public function ITEMS(): HasMany {
    return $this->hasMany(TransactionItem::class, 'id_transaction', 'id' );
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $idProduct = $hubleto->router()->urlParamAsInteger("idEntry");

    if ($idProduct > 0) $query = $query->where($this->table . '.id_product', $idProduct);

    return $query;
  }

}
