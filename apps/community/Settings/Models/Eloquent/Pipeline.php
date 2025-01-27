<?php

namespace HubletoApp\Community\Settings\Models\Eloquent;

use HubletoApp\Community\Settings\Models\Eloquent\User;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pipeline extends \HubletoMain\Core\ModelEloquent
{
  public $table = 'pipelines';

  public function PIPELINE_STEPS(): \Illuminate\Database\Query\Builder
  {
    return $this->hasMany(PipelineStep::class, 'id_pipeline', 'id' )->orderBy('order', 'asc');
  }
}
