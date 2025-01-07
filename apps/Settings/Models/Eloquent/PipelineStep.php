<?php

namespace CeremonyCrmMod\Settings\Models\Eloquent;

use CeremonyCrmMod\Settings\Models\Eloquent\User;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PipelineStep extends \ADIOS\Core\Model\Eloquent
{
  public $table = 'pipeline_steps';

  public function PIPELINE(): BelongsTo {
    return $this->belongsTo(Pipeline::class, 'id_pipeline','id' );
  }

}
