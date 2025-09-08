<?php

namespace Hubleto\App\Community\Crypto\Controllers;

class Dashboard extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'crypto', 'content' => $this->translate('Crypto') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Crypto/Dashboard.twig');
  }

}
