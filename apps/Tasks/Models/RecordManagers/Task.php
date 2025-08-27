<?php

namespace Hubleto\App\Community\Tasks\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Hubleto\App\Community\Settings\Models\RecordManagers\User;
use Hubleto\App\Community\Projects\Models\RecordManagers\Project;
use Hubleto\App\Community\Pipeline\Models\RecordManagers\Pipeline;
use Hubleto\App\Community\Pipeline\Models\RecordManagers\PipelineStep;
use Hubleto\App\Community\Deals\Models\RecordManagers\DealTask;
use Hubleto\App\Community\Projects\Models\RecordManagers\ProjectTask;

class Task extends \Hubleto\Erp\RecordManager
{
  public $table = 'tasks';

  public function PROJECT(): BelongsTo
  {
    return $this->belongsTo(Project::class, 'id_project', 'id');
  }

  public function DEVELOPER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_developer', 'id');
  }

  public function TESTER(): BelongsTo
  {
    return $this->belongsTo(User::class, 'id_tester', 'id');
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

  /** @return HasMany<DealProduct, covariant Deal> */
  public function DEALS(): HasMany
  {
    return $this->hasMany(DealTask::class, 'id_deal', 'id');
  }

  /** @return HasMany<DealProduct, covariant Deal> */
  public function PROJECTS(): HasMany
  {
    return $this->hasMany(ProjectTask::class, 'id_project', 'id');
  }

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \Hubleto\Erp\Loader::getGlobalApp();

    $defaultFilters = $main->getRouter()->urlParamAsArray("defaultFilters");

    $query = Pipeline::applyPipelineStepDefaultFilter(
      $this->model,
      $query,
      $defaultFilters['fTaskPipelineStep'] ?? []
    );

    return $query;
  }

}
