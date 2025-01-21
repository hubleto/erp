<?php

namespace HubletoApp\Community\Deals\Controllers\Api;

use HubletoApp\Community\Settings\Models\Pipeline;
use HubletoApp\Community\Settings\Models\PipelineStep;
use Exception;

class ChangePipeline extends \HubletoMain\Core\Controller
{

  public function renderJson(): ?array
  {
    $mPipeline = new Pipeline($this->main);
    $mPipelineStep = new PipelineStep($this->main);
    $newPipeline = null;

    if (isset($this->main->params["idPipeline"])) {
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
