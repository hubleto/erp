<?php

namespace CeremonyCrmApp\Modules\Sales\Deals\Controllers;

class Deals extends \CeremonyCrmApp\Core\Controller {

  public string $translationContext = 'mod.sales.deals.controllers.deals';

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
    $this->setView('@app/Modules/Sales/Deals/Views/Deals.twig');
  }

}