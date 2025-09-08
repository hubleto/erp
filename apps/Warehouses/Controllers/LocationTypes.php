<?php

namespace Hubleto\App\Community\Warehouses\Controllers;

class LocationTypes extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'warehouses', 'content' => 'Warehouses' ],
      [ 'url' => 'settings', 'content' => 'Settings' ],
      [ 'url' => 'location-types', 'content' => 'Location types' ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Warehouses/LocationTypes.twig');
  }

}
