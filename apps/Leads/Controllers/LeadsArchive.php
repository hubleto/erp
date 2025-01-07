<?php

namespace CeremonyCrmMod\Leads\Controllers;

class LeadsArchive extends \CeremonyCrmApp\Core\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'leads', 'content' => $this->translate('Leads') ],
      [ 'url' => '', 'content' => $this->translate('Archive') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/Leads/Views/LeadsArchive.twig');
  }
}