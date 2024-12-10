<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Controllers;

class DealStatuses extends \CeremonyCrmApp\Core\Controller {

  public string $translationContext = 'mod.core.settings.controllers.dealStatuses';

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->app->translate('Settings') ],
      [ 'url' => 'lead-statuses', 'content' => $this->app->translate('Deal Statuses') ],
    ]);
  }

}