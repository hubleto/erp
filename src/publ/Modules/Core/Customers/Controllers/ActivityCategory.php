<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Controllers;

class ActivityCategory extends \CeremonyCrmApp\Core\Controller {
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'customers', 'content' => $this->app->translate('Customers') ],
      [ 'url' => 'customers/activities', 'content' => $this->app->translate('Activities') ],
      [ 'url' => '', 'content' => $this->app->translate('Categories') ],
    ]);
  }

 }