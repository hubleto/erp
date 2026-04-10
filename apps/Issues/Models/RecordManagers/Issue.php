<?php

namespace Hubleto\App\Community\Issues\Models\RecordManagers;

use Hubleto\App\Community\Customers\Models\RecordManagers\Customer;
use Hubleto\App\Community\Workflow\Models\RecordManagers\Workflow;
use Hubleto\App\Community\Workflow\Models\RecordManagers\WorkflowStep;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Hubleto\App\Community\Auth\Models\RecordManagers\User;

class Issue extends \Hubleto\Erp\RecordManager
{
  public $table = 'issues';

  /** @return HasOne<Customer, covariant Order> */
  public function CUSTOMER(): HasOne
  {
    return $this->hasOne(Customer::class, 'id', 'id_customer');
  }

  /** @return BelongsTo<User, covariant Lead> */
  public function OWNER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_owner', 'id');
  }

  /** @return BelongsTo<User, covariant Lead> */
  public function MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_manager', 'id');
  }

  /** @return HasOne<Workflow, covariant Deal> */
  public function WORKFLOW(): HasOne
  {
    return $this->hasOne(Workflow::class, 'id', 'id_workflow');
  }

  /** @return HasOne<WorkflowStep, covariant Deal> */
  public function WORKFLOW_STEP(): HasOne
  {
    return $this->hasOne(WorkflowStep::class, 'id', 'id_workflow_step');
  }

  /** @return HasMany<DealTask, covariant Deal> */
  public function TASKS(): HasMany
  {
    return $this->hasMany(IssueTask::class, 'id_issue', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    $filters = $hubleto->router()->urlParamAsArray("filters");

    $query = Workflow::applyWorkflowStepFilter(
      $this->model,
      $query,
      (array) ($filters['fIssueWorkflowStep'] ?? [])
    );

    if (isset($filters["fIssueOwnership"])) {
      /** @var \Hubleto\Framework\AuthProvider */
      $authProvider = $hubleto->getService(\Hubleto\Framework\AuthProvider::class);
      $idUser = $authProvider->getUserId();

      switch ($filters["fIssueOwnership"]) {
        case 1: $query = $query->where("issues.id_owner", $idUser); break;
        case 2: $query = $query->where("issues.id_manager", $idUser); break;
      }
    }

    if (isset($filters["fIssueClosed"])) {
      if ($filters["fIssueClosed"] == 0) $query = $query->where("issues.is_closed", false);
      if ($filters["fIssueClosed"] == 1) $query = $query->where("issues.is_closed", true);
    }

    return $query;
  }

}
