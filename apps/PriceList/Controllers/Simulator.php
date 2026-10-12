<?php

namespace Hubleto\App\Community\PriceList\Controllers;

class Simulator extends \Hubleto\Erp\Controller
{
  public string $permission = 'Hubleto/App/Custom/PriceList/Loader::Controllers/Simulator';

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'price-lists', 'content' => $this->translate('Price lists') ],
      [ 'url' => '', 'content' => $this->translate('Simulator') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:PriceList/Simulator.twig');
  }
}
