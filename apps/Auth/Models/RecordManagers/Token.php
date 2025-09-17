<?php

namespace Hubleto\App\Community\Auth\Models\RecordManagers;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class Token extends \Hubleto\Erp\RecordManager {
  public static $snakeAttributes = false;
  public $table = 'tokens';

}
