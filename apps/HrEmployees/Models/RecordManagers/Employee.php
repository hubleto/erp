<?php

namespace Hubleto\App\Community\HrEmployees\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Hubleto\App\Community\Settings\Models\RecordManagers\Team;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends \Hubleto\Erp\RecordManager
{
  public $table = 'hr_employees';

  public function USER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_user', 'id');
  }

  public function TEAM(): BelongsTo
  {
    return $this->belongsTo(Team::class, 'id_team', 'id');
  }

  public function MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_manager', 'id');
  }
}