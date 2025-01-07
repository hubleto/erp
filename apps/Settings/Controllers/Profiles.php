<?php

namespace HubletoApp\Settings\Controllers;

class Profiles extends \HubletoCore\Core\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'profiles', 'content' => $this->translate('Profiles') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/Settings/Views/Profiles.twig');
  }

}