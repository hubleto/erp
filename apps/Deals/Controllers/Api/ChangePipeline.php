<?php

namespace HubletoApp\Community\Deals\Controllers\Api;

use Exception;
use HubletoApp\Community\Pipeline\Models\Pipeline;
use HubletoApp\Community\Pipeline\Models\PipelineStep;

class ChangePipeline extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $mPipeline = $this->main->di->create(Pipeline::class);
    $newPipeline = null;

    if ($this->main->isUrlParam('idPipeline')) {
      try {
        $newPipeline = $mPipeline->record
          ->where("id", $this->main->urlParamAsInteger('idPipeline'))
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
