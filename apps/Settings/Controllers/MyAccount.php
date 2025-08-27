<?php

namespace Hubleto\App\Community\Settings\Controllers;

class MyAccount extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'company', 'content' => $this->translate('Company') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Settings/MyAccount.twig');
  }

}
