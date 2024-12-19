<?php

namespace CeremonyCrmMod\Settings\Controllers;

class ContactTypes extends \CeremonyCrmApp\Core\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'contact-types', 'content' => $this->translate('Contact Types') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@mod/Settings/Views/ContactTypes.twig');
  }

}