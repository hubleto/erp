<?php

namespace Hubleto\App\Community\Settings\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permission extends \Hubleto\Erp\RecordManager
{
  public $table = 'permissions';
}
