<?php

namespace CeremonyCrmApp\Modules\Sales\Sales\Controllers;

use CeremonyCrmApp\Modules\Core\Settings\Models\Pipeline;
use CeremonyCrmApp\Modules\Core\Settings\Models\Setting;
use CeremonyCrmApp\Modules\Sales\Sales\Models\Deal;

class Home extends \CeremonyCrmApp\Core\Controller {
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => '', 'content' => $this->app->translate('Sales') ],
    ]);
  }

  public function prepareViewParams()
  {
    parent::prepareViewParams();
    $mSetting = new Setting($this->app);
    $mPipeline = new Pipeline($this->app);
    $mDeal = new Deal($this->app);

    $defaultPipelineId = $mSetting->eloquent
      ->select("value")
      ->where("key", "Modules\Core\Settings\Pipeline\DefaultPipeline")
      ->first()
      ->toArray()
    ;
    $defaultPipelineId = reset($defaultPipelineId);

    $defaultPipeline = $mPipeline->eloquent
      ->where("id", $defaultPipelineId)
      ->with("PIPELINE_STEPS")
      ->first()
      ->toArray()
    ;

    $deals = $mDeal->eloquent
      ->where("id_pipeline", $defaultPipelineId)
      ->with("CURRENCY")
      ->get()
      ->toArray()
    ;

    $this->viewParams["pipeline"] = $defaultPipeline;
    $this->viewParams["deals"] = $deals;
  }

 }