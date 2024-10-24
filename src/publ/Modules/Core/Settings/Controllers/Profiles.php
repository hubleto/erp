<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Controllers;

class Profiles extends \CeremonyCrmApp\Core\Controller {
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->app->translate('Settings') ],
      [ 'url' => 'profiles', 'content' => $this->app->translate('Profiles') ],
    ]);
  }
}