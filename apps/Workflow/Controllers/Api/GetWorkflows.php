<?php

namespace Hubleto\App\Community\Workflow\Controllers\Api;

use Exception;
use Hubleto\Framework\Helper;
use Hubleto\App\Community\Workflow\Models\Workflow;
use Hubleto\App\Community\Workflow\Models\WorkflowHistory;

class GetWorkflows extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $model = $this->router()->urlParamAsString('model');
    $recordId = $this->router()->urlParamAsString('recordId');

    $model = trim(str_replace('/', '\\', $model), '\\');

    $mWorkflow = $this->getService(Workflow::class);
    $mWorkflowHistory = $this->getService(WorkflowHistory::class);

    $workflows = Helper::keyBy('id', $mWorkflow->record->prepareReadQuery()->get()?->toArray());

    $history = [];
    if (!empty($model) && $recordId > 0) {
      $history = $mWorkflowHistory->record
        ->with(['USER' => function ($query) {
          $query->select('id', 'nick');
        }])
        ->where('model', $model)
        ->where('record_id', $recordId)
        ->orderBy('datetime_change', 'desc')
        ->get()?->toArray()
      ;
    }

    return [
      "status" => "success",
      "workflows" => $workflows,
      "history" => $history,
    ];
  }

}
