<?php

namespace HubletoApp\Community\Suppliers\Controllers;

class Suppliers extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'suppliers', 'content' => $this->translate('Suppliers') ]
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Suppliers/Suppliers.twig');
  }
}
