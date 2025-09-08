<?php

namespace Hubleto\App\Community\Settings\Controllers;

class ActivityTypes extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'activity-types', 'content' => $this->translate('Activity Types') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Settings/ActivityTypes.twig');
  }

}
