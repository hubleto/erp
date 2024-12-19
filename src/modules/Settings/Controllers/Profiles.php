<?php

namespace CeremonyCrmMod\Settings\Controllers;

class Profiles extends \CeremonyCrmApp\Core\Controller {


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
    $this->setView('@mod/Settings/Views/Profiles.twig');
  }

}