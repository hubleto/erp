<?php

namespace CeremonyCrmApp\Modules\Core\Sandbox\Controllers;

class Categories extends \CeremonyCrmApp\Core\Controller {

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'sandbox', 'content' => $this->app->translate('Sandbox') ],
      [ 'url' => '', 'content' => $this->app->translate('Categories') ],
    ]);
  }
  
}