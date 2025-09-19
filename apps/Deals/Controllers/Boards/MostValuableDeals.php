<?php

namespace Hubleto\App\Community\Deals\Controllers\Boards;

use Hubleto\App\Community\Deals\Models\Deal;

class MostValuableDeals extends \Hubleto\Erp\Controller
{
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    /** @var Deal */
    $mDeal = $this->getModel(Deal::class);

    $mostValuableDeals = $mDeal->record->prepareReadQuery()
      ->where($mDeal->table . ".is_archived", 0)
      ->with('CURRENCY')
      ->orderBy("price", "desc")
      ->offset(0)
      ->limit(5)
      ->get()
      ->toArray()
    ;

    $this->viewParams['mostValuableDeals'] = $mostValuableDeals;

    $this->setView('@Hubleto:App:Community:Deals/Boards/MostValuableDeals.twig');
  }

}
