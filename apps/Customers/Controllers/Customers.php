<?php

namespace Hubleto\App\Community\Customers\Controllers;

class Customers extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      // [ 'url' => 'customers', 'content' => $this->translate('Customers') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Customers/Customers.twig');
  }

}
