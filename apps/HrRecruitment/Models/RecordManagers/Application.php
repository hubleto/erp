<?php

namespace Hubleto\App\Community\HrRecruitment\Models\RecordManagers;

use Hubleto\App\Community\HrRecruitment\Models\RecordManagers\Candidate;
use Hubleto\App\Community\HrRecruitment\Models\RecordManagers\JobOpening;
use Hubleto\App\Community\Workflow\Models\RecordManagers\Workflow;
use Hubleto\App\Community\Workflow\Models\RecordManagers\WorkflowStep;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

  public function WORKFLOW(): HasOne
  {
    return $this->hasOne(Workflow::class, 'id', 'id_workflow');
  }

  public function WORKFLOW_STEP(): HasOne
  {
    return $this->hasOne(WorkflowStep::class, 'id', 'id_workflow_step');
  }

  public function addUrlFiltersToQuery(mixed $query): mixed
  {
    $query = parent::addUrlFiltersToQuery($query);
    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $filters = $hubleto->router()->urlParamAsArray('filters');

    return Workflow::applyWorkflowStepFilter(
      $this->model,
      $query,
      (array) ($filters['fApplicationWorkflowStep'] ?? [])
    );
  }
}