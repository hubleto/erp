<?php

namespace CeremonyCrmMod\Core\Settings\Controllers;

class ActivityTypes extends \CeremonyCrmApp\Core\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'activity-types', 'content' => $this->translate('Activity Types') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@mod/Core/Settings/Views/ActivityTypes.twig');
  }

}