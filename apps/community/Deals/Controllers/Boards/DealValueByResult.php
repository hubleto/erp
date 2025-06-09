<?php

namespace HubletoApp\Community\Deals\Controllers\Boards;

use HubletoApp\Community\Deals\Models\Deal;

class DealValueByResult extends \HubletoMain\Core\Controllers\Controller {

  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $mDeal = new Deal($this->main);

    $deals = $mDeal->record->prepareReadQuery()
      ->selectRaw("`{$mDeal->table}`.`deal_result`, SUM(`{$mDeal->table}`.`price`) as price")
      ->where($mDeal->table . ".is_archived", 0)
      ->where($mDeal->table . ".id_owner", $this->main->auth->getUserId())
      ->with('CURRENCY')
      ->groupBy($mDeal->table . '.deal_result')
      ->get()
      ->toArray()
    ;

    $chartData = [
      'labels' => [],
      'values' => [],
      'colors' => [],
    ];

    $results = [
      0 => ['name' => 'Unknown', 'color' => 'black'],
      1 => ['name' => 'Pending', 'color' => 'gray'],
      2 => ['name' => 'Won', 'color' => 'green'],
      3 => ['name' => 'Lost', 'color' => 'red'],
    ];

    foreach ($deals as $deal) {
      $chartData['labels'][] = $results[$deal['deal_result']]['name'];
      $chartData['values'][] = $deal['price'];
      $chartData['colors'][] = $results[$deal['deal_result']]['color'];
    }

    $this->viewParams['chartData'] = $chartData;

    $this->setView('@HubletoApp:Community:Deals/Boards/DealValueByResult.twig');
  }

}