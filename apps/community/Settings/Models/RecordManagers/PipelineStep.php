<?php

namespace HubletoApp\Community\Settings\Models\RecordManagers;

use HubletoApp\Community\Settings\Models\RecordManagers\User;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PipelineStep extends \HubletoMain\Core\RecordManager
{
  public $table = 'pipeline_steps';

  /** @return BelongsTo<Pipeline, covariant PipelineStep> */
  public function PIPELINE(): BelongsTo
  {
    return $this->belongsTo(Pipeline::class, 'id_pipeline','id' );
  }

}
