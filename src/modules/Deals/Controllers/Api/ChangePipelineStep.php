<?php

namespace CeremonyCrmMod\Deals\Controllers\Api;

use CeremonyCrmMod\Settings\Models\Pipeline;
use CeremonyCrmMod\Settings\Models\PipelineStep;
use CeremonyCrmMod\Deals\Models\Deal;
use CeremonyCrmMod\Deals\Models\DealHistory;
use Exception;

class ChangePipelineStep extends \CeremonyCrmApp\Core\Controller
{

  public function renderJson(): ?array
  {
    $mDeal = new Deal($this->app);
    $mDealHistory = new DealHistory($this->app);
    $mPipelineStep = new PipelineStep($this->app);

    $step = null;

    try {
      $deal = $mDeal->eloquent->find($this->app->params["idDeal"]);
      $deal->id_pipeline_step = $this->app->params["idStep"];
      $deal->save();

      $step = $mPipelineStep->eloquent
        ->where("id_pipeline", $this->app->params["idPipeline"])
        ->where("id", $this->app->params["idStep"])
        ->first()
      ;
      $mDealHistory->eloquent->create([
        "change_date" => date("Y-m-d"),
        "id_deal" => $deal->id,
        "description" => "Pipeline step changed to ".$step->name
      ]);
    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    $dealHistory = $mDealHistory->eloquent->where("id_deal", $deal->id)->get();

    return [
      "status" => "success",
      "returnStep" => $step->toArray(),
      "dealHistory" => $dealHistory->toArray()
    ];
  }
}
