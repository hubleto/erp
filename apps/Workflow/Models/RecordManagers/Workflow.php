<?php

namespace Hubleto\App\Community\Workflow\Models\RecordManagers;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Workflow extends \Hubleto\Erp\RecordManager
{
  public $table = 'workflows';

  public function STEPS(): HasMany //@phpstan-ignore-line
  {
    return $this->hasMany(WorkflowStep::class, 'id_workflow', 'id')->orderBy('order', 'asc'); //@phpstan-ignore-line
  }

  public static function applyWorkflowStepFilter(
    mixed $model,
    mixed $query,
    array $steps
  ): mixed
  {
    if (count($steps) > 0) {
      $query = $query->whereIn($model->table . '.id_workflow_step', $steps);
    }
    return $query;
  }
}
