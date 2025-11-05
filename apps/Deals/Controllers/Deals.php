<?php

namespace Hubleto\App\Community\Deals\Controllers;


use Hubleto\App\Community\Deals\Models\Deal;

class Deals extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      // [ 'url' => '', 'content' => $this->translate('Deals') ],
    ]);
  }

  public function prepareView(): void
  {

    /** @var Deal */
    $mDeal = $this->getModel(Deal::class);

    $result = $mDeal->record
      ->selectRaw("COUNT(id) as count, SUM(price_excl_vat) as price_excl_vat")
      ->where("is_archived", 0)
      ->where("id_owner", $this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId())
      ->first()
      ->toArray()
    ;

    parent::prepareView();
    $this->viewParams['result'] = $result;
    if ($this->router()->isUrlParam('add')) {
      $this->viewParams['recordId'] = -1;
    }
    $this->setView('@Hubleto:App:Community:Deals/Deals.twig');
  }

}
