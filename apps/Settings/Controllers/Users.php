<?php

namespace Hubleto\App\Community\Settings\Controllers;

class Users extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'users', 'content' => $this->translate('Users') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Settings/Users.twig');
  }

}
