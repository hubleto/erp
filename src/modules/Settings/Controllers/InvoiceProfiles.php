<?php

namespace CeremonyCrmMod\Settings\Controllers;

class InvoiceProfiles extends \CeremonyCrmApp\Core\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'invoice-profiles', 'content' => $this->translate('Invoice profiles') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@mod/Settings/Views/InvoiceProfiles.twig');
  }

}