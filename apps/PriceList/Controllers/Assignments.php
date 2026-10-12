<?php

namespace Hubleto\App\Community\PriceLists\Controllers;

class Assignments extends \Hubleto\Erp\Controller
{
  public string $permission = 'Hubleto/App/Community/PriceLists/Loader::Controllers/Assignments';

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'price-lists', 'content' => $this->translate('Price lists') ],
      [ 'url' => '', 'content' => $this->translate('Assignments') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:PriceLists/Assignments.twig');
  }
}
