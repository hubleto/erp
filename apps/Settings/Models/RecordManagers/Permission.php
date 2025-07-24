<?php

namespace HubletoApp\Community\Settings\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permission extends \Hubleto\Framework\RecordManager
{
  public $table = 'permissions';
}
