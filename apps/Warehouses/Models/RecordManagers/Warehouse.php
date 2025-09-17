<?php

namespace Hubleto\App\Community\Warehouses\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warehouse extends \Hubleto\Erp\RecordManager
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
