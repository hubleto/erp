<?php

namespace Hubleto\App\Community\PriceLists\Controllers;

class PriceListHasDocuments extends \Hubleto\Erp\Controller
{
  public string $permission = 'Hubleto/App/Community/PriceLists/Loader::Controllers/PriceListHasDocuments';

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'price-lists', 'content' => $this->translate('Price lists') ],
      [ 'url' => '', 'content' => $this->translate('Documents') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:PriceLists/PriceListHasDocuments.twig');
  }
}
