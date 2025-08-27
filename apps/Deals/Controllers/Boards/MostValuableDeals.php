<?php

namespace HubletoApp\Community\Deals\Controllers\Boards;

use HubletoApp\Community\Deals\Models\Deal;

class MostValuableDeals extends \Hubleto\Erp\Controller
{
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $mDeal = $this->getService(Deal::class);

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

    $this->setView('@HubletoApp:Community:Deals/Boards/MostValuableDeals.twig');
  }

}
