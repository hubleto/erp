<?php

namespace HubletoApp\Community\Pipeline\Models\RecordManagers;

use \Illuminate\Database\Eloquent\Relations\HasMany;

class Pipeline extends \HubletoMain\Core\RecordManager
{
  public $table = 'pipelines';

  public function STEPS(): HasMany //@phpstan-ignore-line
  {
    return $this->hasMany(PipelineStep::class, 'id_pipeline', 'id')->orderBy('order', 'asc'); //@phpstan-ignore-line
  }
}
