<?php

namespace CeremonyCrmApp\Modules\Sales\Deals\Controllers;

class DealsArchive extends \CeremonyCrmApp\Core\Controller {
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'sales', 'content' => $this->app->translate('Sales') ],
      [ 'url' => 'sales/leads', 'content' => $this->app->translate('Deals') ],
      [ 'url' => '', 'content' => $this->app->translate('Archive') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/Modules/Sales/Deals/Views/DealsArchive.twig');
  }
}