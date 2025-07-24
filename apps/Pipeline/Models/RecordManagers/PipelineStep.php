<?php

namespace HubletoApp\Community\Pipeline\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PipelineStep extends \Hubleto\Framework\RecordManager
{
  public $table = 'pipeline_steps';

  /** @return BelongsTo<Pipeline, covariant PipelineStep> */
  public function PIPELINE(): BelongsTo
  {
    return $this->belongsTo(Pipeline::class, 'id_pipeline', 'id');
  }

}
