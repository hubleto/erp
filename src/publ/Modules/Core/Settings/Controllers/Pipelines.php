<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Controllers;

class Pipelines extends \CeremonyCrmApp\Core\Controller {
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->app->translate('Settings') ],
      [ 'url' => 'pipelines', 'content' => $this->app->translate('Pipelines') ],
    ]);
  }

 }