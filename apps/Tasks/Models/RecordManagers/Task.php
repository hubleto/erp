<?php

namespace Hubleto\App\Community\Tasks\Models\RecordManagers;


use Hubleto\App\Community\Contacts\Models\RecordManagers\Contact;
use Hubleto\App\Community\Customers\Models\RecordManagers\Customer;
use Hubleto\App\Community\Workflow\Models\RecordManagers\Workflow;
use Hubleto\App\Community\Workflow\Models\RecordManagers\WorkflowStep;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Hubleto\App\Community\Auth\Models\RecordManagers\User;

class Task extends \Hubleto\Erp\RecordManager
{
  public $table = 'tasks';

  public function DEVELOPER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_developer', 'id');
  }

  public function TESTER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_tester', 'id');
  }

  public function CUSTOMER(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'id_customer', 'id');
  }

  public function CONTACT(): BelongsTo
  {
    return $this->belongsTo(Contact::class, 'id_contact', 'id');
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

  /** @return HasMany<Todo, covariant Deal> */
  public function TODO(): HasMany
  {
    return $this->hasMany(Todo::class, 'id_task', 'id');
  }

  // /** @return HasMany<DealProduct, covariant Deal> */
  // public function DEALS(): HasMany
  // {
  //   return $this->hasMany(DealTask::class, 'id_task', 'id');
  // }

  // /** @return HasMany<DealProduct, covariant Deal> */
  // public function PROJECTS(): HasMany
  // {
  //   return $this->hasMany(ProjectTask::class, 'id_task', 'id');
  // }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    $filters = $hubleto->router()->urlParamAsArray("filters");

    $view = $hubleto->router()->urlParamAsString('view');
    if ($view == 'briefOverview') $query = $query->where($this->table . '.is_closed', false);

    $query = $query->with('TODO');

    $query = Workflow::applyWorkflowStepFilter(
      $this->model,
      $query,
      $filters['fTaskWorkflowStep'] ?? []
    );

    if (isset($filters["fTaskClosed"])) {
      if ($filters["fTaskClosed"] == 0) $query = $query->where("tasks.is_closed", false);
      if ($filters["fTaskClosed"] == 1) $query = $query->where("tasks.is_closed", true);
    }

    return $query;
  }

}
