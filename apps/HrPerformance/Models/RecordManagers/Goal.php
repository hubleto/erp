<?php

namespace Hubleto\App\Community\HrPerformance\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Goal extends \Hubleto\Erp\RecordManager
{
  public $table = 'hr_goals';

  public function USER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_user', 'id');
  }
}