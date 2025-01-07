<?php

namespace HubletoApp\Deals\Controllers;

class Deals extends \HubletoCore\Core\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'sales', 'content' => $this->translate('Sales') ],
      [ 'url' => '', 'content' => $this->translate('Deals') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/Deals/Views/Deals.twig');
  }

}