<?php

namespace CeremonyCrmMod\Core\Settings\Controllers;

class UserRoles extends \CeremonyCrmApp\Core\Controller {


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
    $this->setView('@mod/Core/Settings/Views/UserRoles.twig');
  }

}