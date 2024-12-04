<?php

namespace CeremonyCrmApp\Modules\Sales\Sales\Controllers;

class Leads extends \CeremonyCrmApp\Core\Controller {
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'sales', 'content' => $this->app->translate('Sales') ],
      [ 'url' => '', 'content' => $this->app->translate('Leads') ],
    ]);
  }

 }