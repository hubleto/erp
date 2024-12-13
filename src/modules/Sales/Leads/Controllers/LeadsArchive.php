<?php

namespace CeremonyCrmMod\Sales\Leads\Controllers;

class LeadsArchive extends \CeremonyCrmApp\Core\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'sales', 'content' => $this->translate('Sales') ],
      [ 'url' => 'sales/leads', 'content' => $this->translate('Leads') ],
      [ 'url' => '', 'content' => $this->translate('Archive') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@mod/Sales/Leads/Views/LeadsArchive.twig');
  }
}