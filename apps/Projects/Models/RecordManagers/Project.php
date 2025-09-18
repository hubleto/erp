<?php

namespace Hubleto\App\Community\Projects\Models\RecordManagers;


use Hubleto\App\Community\Contacts\Models\RecordManagers\Contact;
use Hubleto\App\Community\Customers\Models\RecordManagers\Customer;
use Hubleto\App\Community\Workflow\Models\RecordManagers\Workflow;
use Hubleto\App\Community\Workflow\Models\RecordManagers\WorkflowStep;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project extends \Hubleto\Erp\RecordManager
{
  public $table = 'projects';

  public function MAIN_DEVELOPER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_main_developer', 'id');
  }

  public function ACCOUNT_MANAGER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_account_manager', 'id');
  }

  public function PHASE(): BelongsTo
  {
    return $this->belongsTo(Phase::class, 'id_phase', 'id');
  }

  public function CUSTOMER(): BelongsTo
  {
    return $this->belongsTo(Customer::class, 'id_customer', 'id');
  }

  public function CONTACT(): BelongsTo
  {
    return $this->belongsTo(Contact::class, 'id_contact', 'id');
  }

  public function OWNER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_owner', 'id');
  }

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

  /** @return hasMany<LeadDocument, covariant Lead> */
  public function ORDERS(): HasMany
  {
    return $this->hasMany(ProjectOrder::class, 'id_project', 'id');
  }

  /** @return HasMany<ProjectTask, covariant Deal> */
  public function TASKS(): HasMany
  {
    return $this->hasMany(ProjectTask::class, 'id_project', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \Hubleto\Erp\Loader::getGlobalApp();

    if ($main->router()->urlParamAsInteger("idDeal") > 0) {
      $query = $query->where($this->table . '.id_deal', $main->router()->urlParamAsInteger("idDeal"));
    }
    
    $filters = $main->router()->urlParamAsArray("filters");
    if ($main->router()->urlParamAsInteger("idDeal") > 0) {
      $query = $query->whereIn($this->table . '.', $main->router()->urlParamAsInteger("idDeal"));
    }

    $query = Workflow::applyWorkflowStepFilter(
      $this->model,
      $query,
      $filters['fProjectWorkflowStep'] ?? []
    );

    if (isset($filters["fProjectClosed"])) {
      if ($filters["fProjectClosed"] == 0) $query = $query->where("projects.is_closed", false);
      if ($filters["fProjectClosed"] == 1) $query = $query->where("projects.is_closed", true);
    }

    return $query;
  }
  
}
