<?php

namespace Hubleto\App\Community\Warehouses\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;

use Hubleto\App\Community\Products\Models\RecordManagers\Product;
use Hubleto\App\Community\Warehouses\Models\RecordManagers\Location;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Inventory extends \Hubleto\Erp\RecordManager
{
  public $table = 'warehouses_inventory';

  public function PRODUCT(): HasOne
  {
    return $this->hasOne(Product::class, 'id', 'id_product');
  }

  public function LOCATION(): HasOne
  {
    return $this->hasOne(Location::class, 'id', 'id_location');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $idProduct = $hubleto->router()->urlParamAsInteger("idProduct");

    if ($idProduct > 0) $query = $query->where($this->table . '.id_product', $idProduct);

    return $query;
  }

}
