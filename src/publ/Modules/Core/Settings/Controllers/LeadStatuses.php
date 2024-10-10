<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Controllers;

class LeadStatuses extends \CeremonyCrmApp\Core\Controller {
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->app->translate('Settings') ],
      [ 'url' => 'lead-statuses', 'content' => $this->app->translate('Lead Statuses') ],
    ]);
  }

 }