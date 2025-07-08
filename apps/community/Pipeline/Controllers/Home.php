<?php

namespace HubletoApp\Community\Pipeline\Controllers;

use HubletoApp\Community\Deals\Models\Deal;
use HubletoApp\Community\Deals\Models\Tag;
use HubletoApp\Community\Settings\Models\Currency;
use HubletoApp\Community\Pipeline\Models\Pipeline;
use HubletoApp\Community\Settings\Models\Setting;

class Home extends \HubletoMain\Core\Controllers\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => '', 'content' => $this->translate('Pipeline') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $fDealResult = $this->main->urlParamAsInteger('fDealResult');
    $fOwner = $this->main->urlParamAsInteger('fOwner');

    $mSetting = new Setting($this->main);
    $mPipeline = new Pipeline($this->main);
    $mDeal = new Deal($this->main);
    $mTag = new Tag($this->main);
    $sumPipelinePrice = 0;

    $pipelines = $mPipeline->record->get();

    $defaultPipelineId = 1;

    $searchPipeline = null;

    if ($this->main->isUrlParam("id_pipeline")) {
      $searchPipeline = (array) $mPipeline->record
        ->where("id", (int) $this->main->urlParamAsInteger("id_pipeline"))
        ->with("STEPS")
        ->first()
        ->toArray()
      ;
    }
    else {
      $searchPipeline = (array) $mPipeline->record
        ->where("id", $defaultPipelineId)
        ->with("STEPS")
        ->first()
        ->toArray()
      ;
    }

    foreach ((array) $searchPipeline["STEPS"] as $key => $step) {
      $step = (array) $step;

      $sumPrice = (float) $mDeal->record
        ->selectRaw("SUM(price) as price")
        ->where("id_pipeline", $searchPipeline["id"])
        ->where("id_pipeline_step", $step["id"])
        ->first()
        ->price
      ;

      $searchPipeline["STEPS"][$key]["sum_price"] = ($step["probability"] / 100) * $sumPrice;
      $sumPipelinePrice += $sumPrice;
    }

    $searchPipeline["price"] = $sumPipelinePrice;

    $deals = $mDeal->record
      ->where("id_pipeline", (int) $searchPipeline["id"])
      ->where("is_closed", false)
      ->with("CURRENCY")
      ->with("CUSTOMER")
      ->with("TAGS")
      ->with("OWNER")
    ;

    if ($fDealResult > 0) $deals = $deals->where('deal_result', $fDealResult ?? true);
    if ($fOwner > 0) $deals = $deals->where('id_owner', $fOwner);

    $deals = $deals
      ->get()
      ->toArray()
    ;

    foreach ((array) $deals as $key => $deal) {
      if (empty($deal["TAGS"])) continue;
      $tag = $mTag->record->find($deal["TAGS"][0]["id_tag"])?->toArray();
      $deals[$key]["TAG"] = $tag;
      unset($deals[$key]["TAGS"]);
    }

    $mSettings = new Setting($this->main);
    $defaultCurrencyId = (int) $mSettings->record
      ->where("key", "Apps\Community\Settings\Currency\DefaultCurrency")
      ->first()
      ->value
    ?? 1;
    $mCurrency = new Currency($this->main);
    $defaultCurrency = (string) $mCurrency->record->find($defaultCurrencyId)->code ?? "";

    $this->viewParams["currency"] = $defaultCurrency;
    $this->viewParams["pipelines"] = $pipelines;
    $this->viewParams["pipeline"] = $searchPipeline;
    $this->viewParams["deals"] = $deals;

    $this->setView('@HubletoApp:Community:Pipeline/Home.twig');
  }

}