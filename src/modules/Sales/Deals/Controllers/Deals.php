<?php

namespace CeremonyCrmMod\Sales\Deals\Controllers;

class Deals extends \CeremonyCrmApp\Core\Controller {


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
    $this->setView('@mod/Sales/Deals/Views/Deals.twig');
  }

}