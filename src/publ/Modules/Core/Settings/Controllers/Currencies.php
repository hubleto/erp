<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Controllers;

class Currencies extends \CeremonyCrmApp\Core\Controller {
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->app->translate('Settings') ],
      [ 'url' => 'currencies', 'content' => $this->app->translate('Currencies') ],
    ]);
  }

 }