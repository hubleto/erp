<?php

namespace Hubleto\App\Community\Deals\Controllers\Api;

use Exception;
use Hubleto\App\Community\Pipeline\Models\Pipeline;
use Hubleto\App\Community\Pipeline\Models\PipelineStep;

class ChangePipeline extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $mPipeline = $this->getService(Pipeline::class);
    $newPipeline = null;

    if ($this->router()->isUrlParam('idPipeline')) {
      try {
        $newPipeline = $mPipeline->record
          ->where("id", $this->router()->urlParamAsInteger('idPipeline'))
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
        "error" => "Pipeline parameter was not defined",
      ];
    }

    return [
      "newPipeline" => $newPipeline,
      "status" => "success"
    ];
  }
}
