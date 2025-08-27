<?php

namespace HubletoApp\Community\Inventory\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use HubletoApp\Community\Settings\Models\RecordManagers\User;
use HubletoApp\Community\Products\Models\RecordManagers\Product;
use HubletoApp\Community\Warehouses\Models\RecordManagers\Location;

class Transaction extends \HubletoMain\RecordManager
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
