<?php

namespace HubletoApp\Settings\Controllers;

class Permissions extends \HubletoCore\Core\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'permissions', 'content' => $this->translate('Permissions') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/Settings/Views/Permissions.twig');
  }

}