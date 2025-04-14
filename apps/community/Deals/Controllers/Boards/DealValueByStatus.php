<?php

namespace HubletoApp\Community\Deals\Controllers\Boards;

use HubletoApp\Community\Deals\Models\Deal;

class DealValueByStatus extends \HubletoMain\Core\Controller {

  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $mDeal = new Deal($this->main);

    $deals = $mDeal->eloquent
      ->selectRaw("id_deal_status, SUM(price) as price")
      ->where("is_archived", 0)
      ->where("id_user", $this->main->auth->getUserId())
      ->with('CURRENCY')
      ->with('STATUS')
      ->groupBy('id_deal_status')
      ->get()
      ->toArray()
    ;

    $chartData = [
      'labels' => [],
      'values' => [],
      'colors' => [],
    ];

    foreach ($deals as $deal) {
      $chartData['labels'][] = $deal['STATUS']['name'];
      $chartData['values'][] = $deal['price'];
      $chartData['colors'][] = $deal['STATUS']['color'];
    }

    $this->viewParams['chartData'] = $chartData;

    $this->setView('@HubletoApp:Community:Deals/Boards/DealValueByStatus.twig');
  }

}