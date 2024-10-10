<?php

namespace CeremonyCrmApp\Modules\Sales\Sales\Controllers;

class Deals extends \CeremonyCrmApp\Core\Controller {
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'Sales', 'content' => $this->app->translate('Sales') ],
      [ 'url' => 'Deals', 'content' => $this->app->translate('Deals') ],
    ]);
  }

 }