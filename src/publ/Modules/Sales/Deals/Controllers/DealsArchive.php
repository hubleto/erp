<?php

namespace CeremonyCrmApp\Modules\Sales\Deals\Controllers;

class DealsArchive extends \CeremonyCrmApp\Core\Controller {

  public string $translationContext = 'mod.sales.deals.controllers.dealsArchive';

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'sales', 'content' => $this->translate('Sales') ],
      [ 'url' => 'sales/leads', 'content' => $this->translate('Deals') ],
      [ 'url' => '', 'content' => $this->translate('Archive') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/Modules/Sales/Deals/Views/DealsArchive.twig');
  }
}