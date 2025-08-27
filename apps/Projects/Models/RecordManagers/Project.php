<?php

namespace HubletoApp\Community\Projects\Models\RecordManagers;

use HubletoApp\Community\Pipeline\Models\RecordManagers\Pipeline;
use HubletoApp\Community\Pipeline\Models\RecordManagers\PipelineStep;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use HubletoApp\Community\Settings\Models\RecordManagers\User;

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

    if ($main->getRouter()->urlParamAsInteger("idDeal") > 0) {
      $query = $query->where($this->table . '.id_deal', $main->getRouter()->urlParamAsInteger("idDeal"));
    }
    
    $defaultFilters = $main->getRouter()->urlParamAsArray("defaultFilters");
    if ($main->getRouter()->urlParamAsInteger("idDeal") > 0) {
      $query = $query->whereIn($this->table . '.', $main->getRouter()->urlParamAsInteger("idDeal"));
    }

    $query = Pipeline::applyPipelineStepDefaultFilter(
      $this->model,
      $query,
      $defaultFilters['fProjectPipelineStep'] ?? []
    );

    return $query;
  }
  
}
