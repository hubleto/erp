<?php

namespace HubletoApp\Community\Goals\Models\RecordManagers;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use \Illuminate\Database\Eloquent\Relations\HasOne;

class GoalValue extends \HubletoMain\Core\RecordManager
{
  public $table = 'goal_values';

}
