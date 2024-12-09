<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Controllers;

class InvoiceProfiles extends \CeremonyCrmApp\Core\Controller {

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->app->translate('Settings') ],
      [ 'url' => 'invoice-profiles', 'content' => $this->app->translate('Invoice profiles') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/Modules/Core/Settings/Views/InvoiceProfiles.twig');
  }

}