<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Controllers;

class Tags extends \CeremonyCrmApp\Core\Controller {
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->app->translate('Settings') ],
      [ 'url' => 'tags', 'content' => $this->app->translate('Tags') ],
    ]);
  }

 }