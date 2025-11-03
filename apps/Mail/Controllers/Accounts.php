<?php

namespace Hubleto\App\Community\Mail\Controllers;

class Accounts extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'mail/accounts', 'content' => $this->translate('Accounts') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@Hubleto:App:Community:Mail/Accounts.twig');
  }

}
