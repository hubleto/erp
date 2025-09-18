<?php

namespace Hubleto\App\Community\Inventory\Models\RecordManagers;


use Hubleto\App\Community\Products\Models\RecordManagers\Product;
use Hubleto\App\Community\Warehouses\Models\RecordManagers\Location;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Hubleto\App\Community\Auth\Models\RecordManagers\User;

class Transaction extends \Hubleto\Erp\RecordManager
{
  public $table = 'inventory_transactions';

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

}
