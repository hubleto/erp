<?php

namespace HubletoApp\Community\Deals\Controllers\Boards;

use HubletoApp\Community\Deals\Models\Deal;

class DealWarnings extends \HubletoMain\Controller
{
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $warningsTotal = 0;

    $mDeal = $this->main->di->create(Deal::class);

    $myDeals = $mDeal->record->prepareReadQuery()
      ->where($mDeal->table . ".is_archived", 0)
      ->orderBy('price', 'desc')
      ->get()
      ->toArray()
    ;

    // open-deals-without-future-plan
    $items = [];

    foreach ($myDeals as $deal) {
      $futureActivities = 0;
      foreach ($deal['ACTIVITIES'] as $activity) {
        if (strtotime($activity['date_start']) > time()) {
          $futureActivities++;
        }
      }
      if (!$deal['is_closed'] && $futureActivities == 0) {
        $items[] = $deal;
        $warningsTotal++;
      }
    }

    $warnings['open-deals-without-future-plan'] = [
      "title" => $this->translate('Open deals without future plan'),
      "items" => $items,
    ];
    //

    $this->viewParams['warningsTotal'] = $warningsTotal;
    $this->viewParams['warnings'] = $warnings;

    $this->setView('@HubletoApp:Community:Deals/Boards/DealWarnings.twig');
  }

}
