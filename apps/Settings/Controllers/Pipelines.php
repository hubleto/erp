<?php

namespace CeremonyCrmMod\Settings\Controllers;

class Pipelines extends \CeremonyCrmApp\Core\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'pipelines', 'content' => $this->translate('Pipelines') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/Settings/Views/Pipelines.twig');
  }
}