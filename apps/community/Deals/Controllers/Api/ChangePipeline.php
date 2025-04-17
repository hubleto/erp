<?php

namespace HubletoApp\Community\Deals\Controllers\Api;

use HubletoApp\Community\Settings\Models\Pipeline;
use HubletoApp\Community\Settings\Models\PipelineStep;
use Exception;

class ChangePipeline extends \HubletoMain\Core\Controller
{
  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;

  public function renderJson(): ?array
  {
    $mPipeline = new Pipeline($this->main);
    $newPipeline = null;

    if ($this->main->isUrlParam('idPipeline')) {
      try {
        $newPipeline = $mPipeline->record
          ->where("id", $this->main->urlParamAsInteger('idPipeline'))
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
