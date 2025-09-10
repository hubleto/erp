<?php

namespace Hubleto\App\Community\Workflow\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowStep extends \Hubleto\Erp\RecordManager
{
  public $table = 'workflow_steps';

  /** @return BelongsTo<Workflow, covariant WorkflowStep> */
  public function WORKFLOW(): BelongsTo
  {
    return $this->belongsTo(Workflow::class, 'id_workflow', 'id');
  }

}
