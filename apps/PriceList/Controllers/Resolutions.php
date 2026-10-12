<?php

namespace Hubleto\App\Community\PriceList\Controllers;

class Resolutions extends \Hubleto\Erp\Controller
{
  public string $permission = 'Hubleto/App/Custom/PriceList/Loader::Controllers/Resolutions';

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
    $this->setView('@Hubleto:App:Custom:PriceList/Resolutions.twig');
  }
}
