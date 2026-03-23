<?php

namespace Hubleto\App\Community\Warehouses\Controllers;

class LocationTypes extends \Hubleto\Erp\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'warehouses', 'content' => $this->translate('Warehouses') ],
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'location-types', 'content' => $this->translate('Location types') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Warehouses/LocationTypes.twig');
  }

}
