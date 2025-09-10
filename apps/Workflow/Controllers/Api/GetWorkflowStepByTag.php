<?php

namespace Hubleto\App\Community\Workflow\Controllers\Api;

use Exception;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;

class GetWorkflowStepByTag extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {

    $idWorkflow = $this->router()->urlParamAsInteger('idWorkflow');
    $tag = $this->router()->urlParamAsString('tag');

    $mWorkflowStep = $this->getService(WorkflowStep::class);

    return $mWorkflowStep->record->where('id_workflow', $idWorkflow)->where('tag', $tag)->first()?->toArray();
  }

}
