<?php

namespace Hubleto\App\Community\HrEmployees\Models\RecordManagers;

use Hubleto\App\Community\Auth\Models\RecordManagers\User;
use Hubleto\App\Community\Settings\Models\RecordManagers\Team;
use Hubleto\App\Community\HrEmployees\Models\RecordManagers\EmploymentType;
use Hubleto\App\Community\HrEmployees\Models\RecordManagers\EmploymentStatus;
use Hubleto\App\Community\HrEmployees\Models\RecordManagers\WorkLocation;
use Hubleto\App\Community\Workflow\Models\RecordManagers\Workflow;
use Hubleto\App\Community\Workflow\Models\RecordManagers\WorkflowStep;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

  public function EMPLOYMENT_TYPE(): BelongsTo
  {
    return $this->belongsTo(EmploymentType::class, 'id_employment_type', 'id');
  }

  public function EMPLOYMENT_STATUS(): BelongsTo
  {
    return $this->belongsTo(EmploymentStatus::class, 'id_employment_status', 'id');
  }

  public function WORK_LOCATION(): BelongsTo
  {
    return $this->belongsTo(WorkLocation::class, 'id_work_location', 'id');
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
      (array) ($filters['fEmployeeWorkflowStep'] ?? [])
    );
  }
}