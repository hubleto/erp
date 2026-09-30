<?php

namespace Hubleto\App\Community\HrPerformance\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Hubleto\App\Community\Workflow\Models\RecordManagers\Workflow;
use Hubleto\App\Community\Workflow\Models\RecordManagers\WorkflowStep;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Review extends \Hubleto\Erp\RecordManager
{
  public $table = 'hr_reviews';

  public function EMPLOYEE(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_user', 'id');
  }

  public function REVIEWER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_reviewer', 'id');
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
      (array) ($filters['fReviewWorkflowStep'] ?? [])
    );
  }
}