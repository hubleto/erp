<?php

namespace CeremonyCrmMod\Core\Settings\Controllers;

class Currencies extends \CeremonyCrmApp\Core\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'currencies', 'content' => $this->translate('Currencies') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@mod/Core/Settings/Views/Currencies.twig');
  }

}