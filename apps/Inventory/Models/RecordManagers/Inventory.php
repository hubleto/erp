<?php

namespace Hubleto\App\Community\Inventory\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends \Hubleto\Erp\RecordManager
{
  public $table = 'inventory';

  public function MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_manager', 'id');
  }

}
