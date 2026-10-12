<?php

namespace Hubleto\App\Community\PriceLists\Controllers;

class PriceLists extends \Hubleto\Erp\Controller
{
  public string $permission = 'Hubleto/App/Community/PriceLists/Loader::Controllers/PriceLists';

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'price-lists', 'content' => $this->translate('Price lists') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:PriceLists/PriceLists.twig');
  }
}
