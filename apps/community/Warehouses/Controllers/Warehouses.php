<?php

namespace HubletoApp\Community\Warehouses\Controllers;

class Warehouses extends \HubletoMain\Core\Controllers\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'warehouses', 'content' => 'Warehouses' ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Warehouses/Warehouses.twig');
  }

}