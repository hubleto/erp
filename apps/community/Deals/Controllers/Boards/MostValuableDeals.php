<?php

namespace HubletoApp\Community\Deals\Controllers\Boards;

use HubletoApp\Community\Deals\Models\Deal;

class MostValuableDeals extends \HubletoMain\Core\Controllers\Controller {

  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $mDeal = new Deal($this->main);

    $mostValuableDeals = $mDeal->record
      ->where("is_archived", 0)
      ->where("id_owner", $this->main->auth->getUserId())
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