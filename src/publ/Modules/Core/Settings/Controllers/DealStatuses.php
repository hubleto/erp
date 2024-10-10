<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Controllers;

class DealStatuses extends \CeremonyCrmApp\Core\Controller {
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->app->translate('Settings') ],
      [ 'url' => 'deal-statuses', 'content' => $this->app->translate('Deal Statuses') ],
    ]);
  }

 }