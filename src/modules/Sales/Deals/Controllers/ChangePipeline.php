<?php

namespace CeremonyCrmMod\Sales\Deals\Controllers;

use CeremonyCrmMod\Core\Settings\Models\Pipeline;
use CeremonyCrmMod\Core\Settings\Models\PipelineStep;
use Exception;

class ChangePipeline extends \CeremonyCrmApp\Core\Controller
{

  public function renderJson(): ?array
  {
    $mPipeline = new Pipeline($this->app);
    $mPipelineStep = new PipelineStep($this->app);
    $newPipeline = null;

    try {
      $newPipeline = $mPipeline->eloquent
        ->where("id", $this->app->params["idPipeline"])
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
