<?php

namespace HubletoApp\Community\Warehouses\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use HubletoApp\Community\Settings\Models\RecordManagers\User;

class Warehouse extends \Hubleto\Framework\RecordManager
{
  public $table = 'warehouses';

  public function OPERATION_MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_operation_manager', 'id');
  }

  public function TYPE(): BelongsTo
  {
    return $this->belongsTo(WarehouseType::class, 'id_type', 'id');
  }

}
