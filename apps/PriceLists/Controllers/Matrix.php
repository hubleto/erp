<?php

namespace Hubleto\App\Community\PriceLists\Controllers;

class Matrix extends \Hubleto\Erp\Controller
{
  public string $permission = 'Hubleto/App/Community/PriceLists/Loader::Controllers/Matrix';

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'price-lists', 'content' => $this->translate('Price lists') ],
      [ 'url' => '', 'content' => $this->translate('Matrix') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->viewParams['idPriceList'] = $this->router()->urlParamAsInteger('idPriceList');
    $this->setView('@Hubleto:App:Community:PriceLists/Matrix.twig');
  }
}
