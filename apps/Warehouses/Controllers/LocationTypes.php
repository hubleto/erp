<?php

namespace HubletoApp\Community\Warehouses\Controllers;

class LocationTypes extends \HubletoMain\Controller
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
    $this->setView('@HubletoApp:Community:Warehouses/LocationTypes.twig');
  }

}
