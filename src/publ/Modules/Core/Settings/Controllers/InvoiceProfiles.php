<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Controllers;

class InvoiceProfiles extends \CeremonyCrmApp\Core\Controller {

  public string $translationContext = 'mod.core.settings.controllers.invoiceProfiles';

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
    $this->setView('@app/Modules/Core/Settings/Views/InvoiceProfiles.twig');
  }

}