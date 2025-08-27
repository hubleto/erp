<?php

namespace HubletoApp\Community\Pipeline\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Pipeline extends \HubletoMain\RecordManager
{
  public $table = 'pipelines';

  public function STEPS(): HasMany //@phpstan-ignore-line
  {
    return $this->hasMany(PipelineStep::class, 'id_pipeline', 'id')->orderBy('order', 'asc'); //@phpstan-ignore-line
  }

  public static function applyPipelineStepDefaultFilter(
    mixed $model,
    mixed $query,
    array $steps
  ): mixed
  {
    if (count($steps) > 0) {
      $query = $query->whereIn($model->table . '.id_pipeline_step', $steps);
    }
    return $query;
  }
}
