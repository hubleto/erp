<?php

namespace Hubleto\App\Community\HrRecruitment\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobOpening extends \Hubleto\Erp\RecordManager
{
  public $table = 'hr_job_openings';

  public function HIRING_MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_hiring_manager', 'id');
  }
}