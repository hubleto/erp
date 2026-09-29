<?php

namespace Hubleto\App\Community\HrRecruitment\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Hubleto\App\Community\HrRecruitment\Models\RecordManagers\EmploymentType;
use Hubleto\App\Community\HrRecruitment\Models\RecordManagers\OpeningDate;
use Hubleto\App\Community\HrRecruitment\Models\RecordManagers\WorkLocation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobOpening extends \Hubleto\Erp\RecordManager
{
  public $table = 'hr_job_openings';

  public function HIRING_MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_hiring_manager', 'id');
  }

  public function EMPLOYMENT_TYPE(): BelongsTo
  {
    return $this->belongsTo(EmploymentType::class, 'id_employment_type', 'id');
  }

  public function WORK_LOCATION(): BelongsTo
  {
    return $this->belongsTo(WorkLocation::class, 'id_work_location', 'id');
  }

  public function OPENING_DATE(): BelongsTo
  {
    return $this->belongsTo(OpeningDate::class, 'id_opening_date', 'id');
  }
}