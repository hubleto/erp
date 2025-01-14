<?php

namespace HubletoApp\Community\Settings\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permission extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'permissions';
}
