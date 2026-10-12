<?php

namespace Hubleto\App\Community\PriceLists\Controllers;

class Resolutions extends \Hubleto\Erp\Controller
{
  public string $permission = 'Hubleto/App/Community/PriceLists/Loader::Controllers/Resolutions';

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'price-lists', 'content' => $this->translate('Price lists') ],
      [ 'url' => '', 'content' => $this->translate('Resolutions') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:PriceLists/Resolutions.twig');
  }
}
