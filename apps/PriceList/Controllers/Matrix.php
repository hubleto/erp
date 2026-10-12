<?php

namespace Hubleto\App\Community\PriceList\Controllers;

class Matrix extends \Hubleto\Erp\Controller
{
  public string $permission = 'Hubleto/App/Custom/PriceList/Loader::Controllers/Matrix';

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
    $this->setView('@Hubleto:App:Custom:PriceList/Matrix.twig');
  }
}
