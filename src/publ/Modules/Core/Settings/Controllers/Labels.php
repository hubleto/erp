<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Controllers;

class Labels extends \CeremonyCrmApp\Core\Controller {

  public string $translationContext = 'mod.core.settings.controllers.labels';

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'labels', 'content' => $this->translate('Labels') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/Modules/Core/Settings/Views/Labels.twig');
  }

}