<?php

namespace CeremonyCrmMod\Settings\Controllers;

class LeadStatuses extends \CeremonyCrmApp\Core\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'lead-statuses', 'content' => $this->translate('Lead Statuses') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@mod/Settings/Views/LeadStatuses.twig');
  }

}