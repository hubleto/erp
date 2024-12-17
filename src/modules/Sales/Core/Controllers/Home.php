<?php

namespace CeremonyCrmMod\Sales\Core\Controllers;

use CeremonyCrmMod\Core\Settings\Models\Label;
use CeremonyCrmMod\Core\Settings\Models\Pipeline;
use CeremonyCrmMod\Core\Settings\Models\Setting;
use CeremonyCrmMod\Sales\Deals\Models\Deal;

class Home extends \CeremonyCrmApp\Core\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => '', 'content' => $this->translate('Sales') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $mSetting = new Setting($this->app);
    $mPipeline = new Pipeline($this->app);
    $mDeal = new Deal($this->app);
    $mLabel = new Label($this->app);
    $sumPipelinePrice = 0;

    $pipelines = $mPipeline->eloquent->get();

    $defaultPipelineId = $mSetting->eloquent
      ->select("value")
      ->where("key", "Modules\Core\Settings\Pipeline\DefaultPipeline")
      ->first()
      ->toArray()
    ;
    $defaultPipelineId = reset($defaultPipelineId);

    $searchPipeline = null;
    if (isset($this->app->params["id_pipeline"])){
      $searchPipeline = $mPipeline->eloquent
        ->where("id", (int) $this->app->params["id_pipeline"])
        ->with("PIPELINE_STEPS")
        ->first()
        ->toArray()
      ;
    }
    else {
      $searchPipeline = $mPipeline->eloquent
        ->where("id", (int) $defaultPipelineId)
        ->with("PIPELINE_STEPS")
        ->first()
        ->toArray()
      ;
    }

    foreach ($searchPipeline["PIPELINE_STEPS"] as $key => $step) {
      $sumPrice = $mDeal->eloquent
        ->selectRaw("SUM(price) as price")
        ->where("id_pipeline", $searchPipeline["id"])
        ->where("id_pipeline_step", $step["id"])
        ->first()
        ->price
      ;
      $searchPipeline["PIPELINE_STEPS"][$key]["sum_price"] = $sumPrice;
      $sumPipelinePrice += $sumPrice;
    }

    $searchPipeline["price"] = $sumPipelinePrice;

    $deals = $mDeal->eloquent
      ->where("id_pipeline", (int) $searchPipeline["id"])
      ->with("CURRENCY")
      ->with("COMPANY")
      ->with("LABELS")
      ->get()
      ->toArray()
    ;

    foreach ($deals as $key => $deal) {
      $label = $mLabel->eloquent->find($deal["LABELS"][0]["id_label"])?->toArray();
      $deals[$key]["LABEL"] = $label;
      unset($deals[$key]["LABELS"]);
    }

    //var_dump($deals); exit;
    $this->viewParams["pipelines"] = $pipelines;
    $this->viewParams["pipeline"] = $searchPipeline;
    $this->viewParams["deals"] = $deals;

    $this->setView('@mod/Sales/Core/Views/Home.twig');
  }

}