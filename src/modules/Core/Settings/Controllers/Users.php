<?php

namespace CeremonyCrmMod\Core\Settings\Controllers;

class Users extends \CeremonyCrmApp\Core\Controller {


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
    $this->setView('@mod/Core/Settings/Views/Users.twig');
  }

}