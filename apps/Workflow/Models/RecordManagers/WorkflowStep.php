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

  public function prepareReadQuery(mixed $query = null, int $level = 0, array|null $includeRelations = null): mixed
  {
    $query = parent::prepareReadQuery($query, $level, $includeRelations);

    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();

    $idWorkflow = $hubleto->router()->urlParamAsInteger('idWorkflow');
    if ($idWorkflow > 0) $query->where('id_workflow', $idWorkflow);

    return $query;
  }
}
