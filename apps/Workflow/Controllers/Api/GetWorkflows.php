<?php

namespace Hubleto\App\Community\Workflow\Controllers\Api;

use Exception;
use Hubleto\App\Community\Workflow\Models\Workflow;

class GetWorkflows extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {

    $mWorkflow = $this->getService(Workflow::class);
    $workflows = \Hubleto\Framework\Helper::keyBy('id', $mWorkflow->record->prepareReadQuery()->get()?->toArray());

    return [
      "status" => "success",
      "workflows" => $workflows,
    ];
  }

}
