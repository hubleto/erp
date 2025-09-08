<?php

namespace Hubleto\App\Community\Settings\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ActivityType extends \Hubleto\Erp\RecordManager
{
  public $table = 'activity_types';
}
