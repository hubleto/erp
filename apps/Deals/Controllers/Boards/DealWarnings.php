<?php

namespace Hubleto\App\Community\Deals\Controllers\Boards;

use Hubleto\App\Community\Deals\Models\Deal;

class DealWarnings extends \Hubleto\Erp\Controller
{
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $warningsTotal = 0;

    /** @var Deal */
    $mDeal = $this->getModel(Deal::class);

    $myDeals = $mDeal->record->prepareReadQuery()
      ->orderBy('price_excl_vat', 'desc')
      ->get()
      ->toArray()
    ;

    // open-deals-without-future-plan
    $items = [];

    foreach ($myDeals as $deal) {
      $futureActivities = 0;
      foreach ($deal['ACTIVITIES'] as $activity) {
        if (strtotime($activity['date_start'] . ' ' . $activity['time_start']) > time()) {
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
      "titleCssClass" => "bg-red-400 p-2 text-white",
      "items" => $items,
    ];
    //

    $this->viewParams['warningsTotal'] = $warningsTotal;
    $this->viewParams['warnings'] = $warnings;

    $this->setView('@Hubleto:App:Community:Deals/Boards/DealWarnings.twig');
  }

}
