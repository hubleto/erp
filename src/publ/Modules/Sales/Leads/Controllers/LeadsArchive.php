<?php

namespace CeremonyCrmApp\Modules\Sales\Leads\Controllers;

class LeadsArchive extends \CeremonyCrmApp\Core\Controller {
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'sales', 'content' => $this->app->translate('Sales') ],
      [ 'url' => 'sales/leads', 'content' => $this->app->translate('Leads') ],
      [ 'url' => '', 'content' => $this->app->translate('Archive') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/Modules/Sales/Leads/Views/LeadsArchive.twig');
  }
}