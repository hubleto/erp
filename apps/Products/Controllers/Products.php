<?php

namespace Hubleto\App\Community\Products\Controllers;

class Products extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      // [ 'url' => '', 'content' => $this->translate('Products') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Products/Products.twig');
  }
}
