<?php

namespace HubletoApp\Community\Deals\Controllers\Api;

use HubletoApp\Community\Settings\Models\Pipeline;
use HubletoApp\Community\Settings\Models\PipelineStep;
use HubletoApp\Community\Deals\Models\Deal;
use HubletoApp\Community\Deals\Models\DealHistory;
use Exception;

class ChangePipelineStep extends \HubletoMain\Core\Controller
{

  public function renderJson(): ?array
  {
    $mDeal = new Deal($this->main);
    $mDealHistory = new DealHistory($this->main);
    $mPipelineStep = new PipelineStep($this->main);

    $step = null;

    try {
      $deal = $mDeal->eloquent->find($this->main->params["idDeal"]);
      $deal->id_pipeline_step = $this->main->params["idStep"];
      $deal->save();

      $step = $mPipelineStep->eloquent
        ->where("id_pipeline", $this->main->params["idPipeline"])
        ->where("id", $this->main->params["idStep"])
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
