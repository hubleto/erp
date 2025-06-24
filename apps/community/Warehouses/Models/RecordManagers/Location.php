<?php

namespace HubletoApp\Community\Warehouses\Models\RecordManagers;

use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use \HubletoApp\Community\Settings\Models\RecordManagers\User;

class Location extends \HubletoMain\Core\RecordManager
{

  public $table = 'warehouses_locations';

  public function OPERATION_MANAGER(): BelongsTo {
    return $this->belongsTo(User::class, 'id_operation_manager', 'id');
  }

  public function TYPE(): BelongsTo {
    return $this->belongsTo(LocationType::class, 'id_type', 'id');
  }

}
