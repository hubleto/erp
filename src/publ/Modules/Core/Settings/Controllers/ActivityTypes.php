<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Controllers;

class ActivityTypes extends \CeremonyCrmApp\Core\Controller {
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->app->translate('Settings') ],
      [ 'url' => 'activity-types', 'content' => $this->app->translate('Activity Types') ],
    ]);
  }

 }