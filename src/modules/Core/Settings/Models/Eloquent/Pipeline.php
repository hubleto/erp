<?php

namespace CeremonyCrmMod\Core\Settings\Models\Eloquent;

use CeremonyCrmMod\Core\Settings\Models\Eloquent\User;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pipeline extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'pipelines';

  public function PIPELINE_STEPS(): HasMany {
    return $this->hasMany(PipelineStep::class, 'id_pipeline', 'id' )->orderBy('order', 'asc');
  }
}
