<?php

namespace HubletoApp\Community\Settings\Models\Eloquent;

use \Illuminate\Database\Eloquent\Relations\HasMany;

class Pipeline extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'pipelines';

  /** @return HasMany<PipelineStep, covariant Pipeline> */
  public function PIPELINE_STEPS(): HasMany
  {
    return $this->hasMany(PipelineStep::class, 'id_pipeline', 'id' )->orderBy('order', 'asc');
  }
}
