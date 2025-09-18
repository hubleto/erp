<?php

namespace Hubleto\App\Community\Inventory\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;

use Hubleto\App\Community\Products\Models\RecordManagers\Product;
use Hubleto\App\Community\Warehouses\Models\RecordManagers\Location;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Inventory extends \Hubleto\Erp\RecordManager
{
  public $table = 'inventory';

  public function PRODUCT(): HasOne
  {
    return $this->hasOne(Product::class, 'id', 'id_product');
  }

  public function STATUS(): HasOne
  {
    return $this->hasOne(Status::class, 'id', 'id_status');
  }

  public function LOCATION(): HasOne
  {
    return $this->hasOne(Location::class, 'id', 'id_location');
  }

}
