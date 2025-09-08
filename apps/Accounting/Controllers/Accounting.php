<?php

namespace Hubleto\App\Community\Accounting\Controllers;

class Accounting extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'accounting', 'content' => $this->translate('Accounting') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@Hubleto:App:Community:Accounting/Accounting.twig');
  }

}
