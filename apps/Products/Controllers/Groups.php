<?php

namespace Hubleto\App\Community\Products\Controllers;

class Groups extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'products', 'content' => $this->translate('Products') ],
      [ 'url' => '', 'content' => $this->translate('Product Groups') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Products/Groups.twig');
  }
}
