<?php

namespace Hubleto\App\Community\Deals\Controllers\Api;

use Exception;
use Hubleto\App\Community\Workflow\Models\Workflow;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;

class ChangeWorkflow extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $mWorkflow = $this->getService(Workflow::class);
    $newWorkflow = null;

    if ($this->router()->isUrlParam('idWorkflow')) {
      try {
        $newWorkflow = $mWorkflow->record
          ->where("id", $this->router()->urlParamAsInteger('idWorkflow'))
          ->with("STEPS")
          ->first()
          ->toArray()
        ;
      } catch (Exception $e) {
        return [
          "status" => "failed",
          "error" => $e
        ];
      }
    } else {
      return [
        "status" => "failed",
        "error" => "Workflow parameter was not defined",
      ];
    }

    return [
      "newWorkflow" => $newWorkflow,
      "status" => "success"
    ];
  }
}
