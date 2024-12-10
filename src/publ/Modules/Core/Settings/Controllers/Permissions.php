<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Controllers;

class Permissions extends \CeremonyCrmApp\Core\Controller {

  public string $translationContext = 'mod.core.settings.controllers.permissions';

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
    $this->setView('@app/Modules/Core/Settings/Views/Permissions.twig');
  }

}