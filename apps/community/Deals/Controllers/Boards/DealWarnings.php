<?php

namespace HubletoApp\Community\Deals\Controllers\Boards;

use HubletoApp\Community\Deals\Models\Deal;

class DealWarnings extends \HubletoMain\Core\Controllers\Controller {

  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $warningsTotal = 0;

    $mDeal = new Deal($this->main);

    $myDeals = $mDeal->record->prepareReadQuery()
      ->where($mDeal->table . ".is_archived", 0)
      ->get()
      ->toArray()
    ;

    // pending-deals-without-future-plan
    $items = [];

    foreach ($myDeals as $deal) {
      $futureActivities = 0;
      foreach ($deal['ACTIVITIES'] as $activity) {
        if (strtotime($activity['date_start']) > time()) $futureActivities++;
      }
      if ($deal['deal_result'] == Deal::RESULT_PENDING && $futureActivities == 0) {
        $items[] = $deal;
        $warningsTotal++;
      }
    }

    $warnings['pending-deals-without-future-plan'] = [
      "title" => $this->translate('Pending deals without future plan'),
      "items" => $items,
    ];
    //

    $this->viewParams['warningsTotal'] = $warningsTotal;
    $this->viewParams['warnings'] = $warnings;

    $this->setView('@HubletoApp:Community:Deals/Boards/DealWarnings.twig');
  }

}