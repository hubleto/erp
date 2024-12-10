<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Controllers;

class ActivityTypes extends \CeremonyCrmApp\Core\Controller {

  public string $translationContext = 'mod.core.settings.controllers.activityType';

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
    $this->setView('@app/Modules/Core/Settings/Views/ActivityTypes.twig');
  }

}