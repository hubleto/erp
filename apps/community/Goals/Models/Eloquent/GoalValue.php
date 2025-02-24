<?php

namespace HubletoApp\Community\Goals\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use \Illuminate\Database\Eloquent\Relations\HasOne;

class GoalValue extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'goal_values';

}
