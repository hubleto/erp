<?php

namespace Hubleto\App\Community\HrRecruitment\Models\RecordManagers;

use Hubleto\App\Community\HrRecruitment\Models\RecordManagers\Candidate;
use Hubleto\App\Community\HrRecruitment\Models\RecordManagers\JobOpening;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends \Hubleto\Erp\RecordManager
{
  public $table = 'hr_applications';

  public function JOB_OPENING(): BelongsTo
  {
    return $this->belongsTo(JobOpening::class, 'id_job_opening', 'id');
  }

  public function CANDIDATE(): BelongsTo
  {
    return $this->belongsTo(Candidate::class, 'id_candidate', 'id');
  }
}