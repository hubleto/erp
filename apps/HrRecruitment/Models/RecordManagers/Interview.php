<?php

namespace Hubleto\App\Community\HrRecruitment\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Hubleto\App\Community\HrRecruitment\Models\RecordManagers\Application;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interview extends \Hubleto\Erp\RecordManager
{
  public $table = 'hr_interviews';

  public function APPLICATION(): BelongsTo
  {
    return $this->belongsTo(Application::class, 'id_application', 'id');
  }

  public function INTERVIEWER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_interviewer', 'id');
  }
}