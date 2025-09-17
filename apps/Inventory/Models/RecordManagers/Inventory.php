<?php

namespace Hubleto\App\Community\Inventory\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Hubleto\App\Community\Settings\Models\RecordManagers\User;

class Inventory extends \Hubleto\Erp\RecordManager
{
  public $table = 'inventory';

  public function MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_manager', 'id');
  }

}
