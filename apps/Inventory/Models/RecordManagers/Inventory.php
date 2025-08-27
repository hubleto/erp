<?php

namespace HubletoApp\Community\Inventory\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use HubletoApp\Community\Settings\Models\RecordManagers\User;

class Inventory extends \HubletoMain\RecordManager
{
  public $table = 'inventory';

  public function MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_manager', 'id');
  }

}
