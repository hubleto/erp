<?php

namespace HubletoApp\Community\Warehouses\Controllers;

class Locations extends \HubletoMain\Core\Controllers\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'locations', 'content' => 'Locations' ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Locations/Locations.twig');
  }

}