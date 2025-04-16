<?php

namespace HubletoApp\Community\Settings\Models\RecordManagers;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permission extends \HubletoMain\Core\RecordManager
{
  public $table = 'permissions';
}
