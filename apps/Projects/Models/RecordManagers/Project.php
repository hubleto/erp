<?php

namespace Hubleto\App\Community\Projects\Models\RecordManagers;

use Hubleto\App\Community\Pipeline\Models\RecordManagers\Pipeline;
use Hubleto\App\Community\Pipeline\Models\RecordManagers\PipelineStep;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Hubleto\App\Community\Settings\Models\RecordManagers\User;
use Hubleto\App\Community\Customers\Models\RecordManagers\Customer;
use Hubleto\App\Community\Contacts\Models\RecordManagers\Contact;

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

  /** @return HasOne<Pipeline, covariant Deal> */
  public function PIPELINE(): HasOne
  {
    return $this->hasOne(Pipeline::class, 'id', 'id_pipeline');
  }

  /** @return HasOne<PipelineStep, covariant Deal> */
  public function PIPELINE_STEP(): HasOne
  {
    return $this->hasOne(PipelineStep::class, 'id', 'id_pipeline_step');
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

    $query = Pipeline::applyPipelineStepFilter(
      $this->model,
      $query,
      $filters['fProjectPipelineStep'] ?? []
    );

    if (isset($filters["fProjectClosed"])) {
      if ($filters["fProjectClosed"] == 0) $query = $query->where("projects.is_closed", false);
      if ($filters["fProjectClosed"] == 1) $query = $query->where("projects.is_closed", true);
    }

    return $query;
  }
  
}
