<?php

namespace HubletoApp\Community\Deals\Controllers;

use HubletoApp\Community\Deals\Models\Deal;

class Deals extends \HubletoMain\Core\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'sales', 'content' => $this->translate('Sales') ],
      [ 'url' => '', 'content' => $this->translate('Deals') ],
    ]);
  }

  public function prepareView(): void
  {

    $mDeal = new Deal($this->main);

    $result = $mDeal->eloquent
      ->selectRaw("COUNT(id) as count, SUM(price) as price")
      ->where("is_archived", 0)
      ->where("id_user", $this->main->auth->getUserId())
      ->get()
      ->toArray()
    ;

    parent::prepareView();
    $this->viewParams["result"] = reset($result);
    $this->setView('@HubletoApp:Community:Deals/Deals.twig');
  }

}