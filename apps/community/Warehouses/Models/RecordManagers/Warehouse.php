<?php

namespace HubletoApp\Community\Warehouses\Models\RecordManagers;

use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use \HubletoApp\Community\Settings\Models\RecordManagers\User;

class Warehouse extends \HubletoMain\Core\RecordManager
{

  public $table = 'warehouses';

  public function MANAGER(): BelongsTo {
    return $this->belongsTo(User::class, 'id_manager', 'id');
  }

}
