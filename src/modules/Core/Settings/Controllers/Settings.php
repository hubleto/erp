<?php

namespace CeremonyCrmMod\Core\Settings\Controllers;

class Settings extends \CeremonyCrmApp\Core\Controller {

  public string $translationContext = 'mod.core.settings.controllers.settings';

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@mod/Core/Settings/Views/Settings.twig');
  }

}