<?php

namespace CeremonyCrmMod\Sales\Core\Controllers;

use CeremonyCrmMod\Core\Settings\Models\Pipeline;
use CeremonyCrmMod\Core\Settings\Models\Setting;
use CeremonyCrmMod\Sales\Deals\Models\Deal;

class Home extends \CeremonyCrmApp\Core\Controller {

  public string $translationContext = 'mod.sales.core.controllers.home';

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

    $deals = $mDeal->eloquent
      ->where("id_pipeline", (int) $searchPipeline["id"])
      ->with("CURRENCY")
      ->with("COMPANY")
      ->get()
      ->toArray()
    ;

    $this->viewParams["pipelines"] = $pipelines;
    $this->viewParams["pipeline"] = $searchPipeline;
    $this->viewParams["deals"] = $deals;

    $this->setView('@mod/Sales/Core/Views/Home.twig');
  }

}