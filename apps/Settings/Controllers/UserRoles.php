<?php

namespace HubletoApp\Community\Settings\Controllers;

class UserRoles extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'user-roles', 'content' => $this->translate('User Roles') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Settings/UserRoles.twig');
  }

}
