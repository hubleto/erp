<?php

namespace HubletoApp\Community\Goals\Models\RecordManagers;

use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use \Illuminate\Database\Eloquent\Relations\HasOne;

class Goal extends \HubletoMain\Core\RecordManager
{
  public $table = 'goals';

  /** @return HasMany<GoalValue, covariant Goal> */
  public function GOALS(): HasMany {
    return $this->hasMany(GoalValue::class, 'id_goal', 'id');
  }
}
