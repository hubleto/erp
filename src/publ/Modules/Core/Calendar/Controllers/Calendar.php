<?php

namespace CeremonyCrmApp\Modules\Core\Calendar\Controllers;

class Calendar extends \CeremonyCrmApp\Core\Controller {
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'calendar', 'content' => $this->app->translate('Calendar') ],
      [ 'url' => '', 'content' => $this->app->translate('Calendar') ],
    ]);
  }

 }