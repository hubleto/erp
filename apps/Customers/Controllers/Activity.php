<?php

namespace Hubleto\App\Community\Customers\Controllers;

class Activity extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'customers', 'content' => $this->translate('Customers') ],
      [ 'url' => '', 'content' => $this->translate('Activities') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Customers/Activity.twig');
  }

}
