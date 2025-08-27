<?php

namespace HubletoApp\Community\Tasks\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use HubletoApp\Community\Settings\Models\RecordManagers\User;
use HubletoApp\Community\Projects\Models\RecordManagers\Project;
use HubletoApp\Community\Pipeline\Models\RecordManagers\Pipeline;
use HubletoApp\Community\Pipeline\Models\RecordManagers\PipelineStep;

class Task extends \HubletoMain\RecordManager
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

  public function prepareReadQuery(mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareReadQuery($query, $level);

    $main = \HubletoMain\Loader::getGlobalApp();

    $defaultFilters = $main->getRouter()->urlParamAsArray("defaultFilters");

    $query = Pipeline::applyPipelineStepDefaultFilter(
      $this->model,
      $query,
      $defaultFilters['fTaskPipelineStep'] ?? []
    );

    return $query;
  }

}
