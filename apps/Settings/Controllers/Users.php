<?php

namespace HubletoApp\Settings\Controllers;

class Users extends \HubletoCore\Core\Controller {


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
    $this->setView('@app/Settings/Views/Users.twig');
  }

}