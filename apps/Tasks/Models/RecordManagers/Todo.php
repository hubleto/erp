<?php

namespace Hubleto\App\Community\Tasks\Models\RecordManagers;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Hubleto\App\Community\Auth\Models\RecordManagers\User;

class Todo extends \Hubleto\Erp\RecordManager
{
  public $table = 'tasks_todo';

  public function RESPONSIBLE(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_responsible', 'id');
  }

  public function TASK(): BelongsTo
  {
    return $this->belongsTo(Task::class, 'id_task', 'id');
  }

}
