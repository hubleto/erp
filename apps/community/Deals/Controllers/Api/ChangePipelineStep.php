<?php

namespace HubletoApp\Community\Deals\Controllers\Api;

use HubletoApp\Community\Settings\Models\Pipeline;
use HubletoApp\Community\Settings\Models\PipelineStep;
use HubletoApp\Community\Deals\Models\Deal;
use HubletoApp\Community\Deals\Models\DealHistory;
use Exception;

class ChangePipelineStep extends \HubletoMain\Core\Controller
{
  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;

  public function renderJson(): ?array
  {
    $mDeal = new Deal($this->main);
    $mDealHistory = new DealHistory($this->main);
    $mPipelineStep = new PipelineStep($this->main);

    $step = null;

    if ($this->main->isUrlParam("idDeal") && $this->main->isUrlParam("idStep")) {
      try {
        $deal = $mDeal->eloquent->find($this->main->urlParamAsInteger("idDeal"));
        $deal->id_pipeline_step = $this->main->urlParamAsInteger("idStep");
        $deal->save();

        $step = $mPipelineStep->eloquent
          ->where("id_pipeline", $this->main->urlParamAsInteger("idPipeline"))
          ->where("id", $this->main->urlParamAsInteger("idStep"))
          ->first()
        ;
        $mDealHistory->eloquent->create([
          "change_date" => date("Y-m-d"),
          "id_deal" => $deal->id,
          "description" => "Pipeline step changed to " . (string) $step->name
        ]);
      } catch (Exception $e) {
        return [
          "status" => "failed",
          "error" => $e
        ];
      }
    } else {
      return [
        "status" => "failed",
        "error" => "Required parameters were not defined."
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
