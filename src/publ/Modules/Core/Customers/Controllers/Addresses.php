<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Controllers;

class Addresses extends \CeremonyCrmApp\Core\Controller {
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'customers', 'content' => $this->app->translate('Customers') ],
      [ 'url' => '', 'content' => $this->app->translate('Person Addresses') ],
    ]);
  }

 }