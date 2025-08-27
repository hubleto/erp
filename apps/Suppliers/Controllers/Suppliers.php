<?php

namespace Hubleto\App\Community\Suppliers\Controllers;

class Suppliers extends \Hubleto\Erp\Controller
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
    $this->setView('@Hubleto:App:Community:Suppliers/Suppliers.twig');
  }
}
