<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Controllers;

class Countries extends \CeremonyCrmApp\Core\Controller {
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->app->translate('Settings') ],
      [ 'url' => 'countries', 'content' => $this->app->translate('Countries') ],
    ]);
  }

 }