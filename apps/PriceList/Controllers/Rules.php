<?php

namespace Hubleto\App\Community\PriceList\Controllers;

class Rules extends \Hubleto\Erp\Controller
{
  public string $permission = 'Hubleto/App/Custom/PriceList/Loader::Controllers/Rules';

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'price-lists', 'content' => $this->translate('Price lists') ],
      [ 'url' => '', 'content' => $this->translate('Rules') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:PriceList/Rules.twig');
  }
}
