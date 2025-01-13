<?php

namespace HubletoApp\Deals\Controllers\Api;

use HubletoApp\Settings\Models\Pipeline;
use HubletoApp\Settings\Models\PipelineStep;
use Exception;

class ChangePipeline extends \HubletoMain\Core\Controller
{

  public function renderJson(): ?array
  {
    $mPipeline = new Pipeline($this->main);
    $mPipelineStep = new PipelineStep($this->main);
    $newPipeline = null;

    try {
      $newPipeline = $mPipeline->eloquent
        ->where("id", $this->main->params["idPipeline"])
        ->with("PIPELINE_STEPS")
        ->first()
        ->toArray()
      ;
    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    return [
      "newPipeline" => $newPipeline,
      "status" => "success"
    ];
  }
}
