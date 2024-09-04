<?php

namespace CeremonyCrmApp\Modules\Core\Services\Controllers;

class Services extends \CeremonyCrmApp\Core\Controller {
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => '', 'content' => $this->app->translate('Services') ],
    ]);
  }

 }