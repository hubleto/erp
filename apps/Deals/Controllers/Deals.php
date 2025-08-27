<?php

namespace HubletoApp\Community\Deals\Controllers;

use HubletoApp\Community\Deals\Models\Deal;

class Deals extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => '', 'content' => $this->translate('Deals') ],
    ]);
  }

  public function prepareView(): void
  {

    $mDeal = $this->getService(Deal::class);

    $result = $mDeal->record
      ->selectRaw("COUNT(id) as count, SUM(price_excl_vat) as price_excl_vat")
      ->where("is_archived", 0)
      ->where("id_owner", $this->getAuthProvider()->getUserId())
      ->first()
      ->toArray()
    ;

    parent::prepareView();
    $this->viewParams['result'] = $result;
    if ($this->getRouter()->isUrlParam('add')) {
      $this->viewParams['recordId'] = -1;
    }
    $this->setView('@HubletoApp:Community:Deals/Deals.twig');
  }

}
