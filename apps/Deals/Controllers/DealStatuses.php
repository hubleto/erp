<?php

namespace HubletoApp\Deals\Controllers;

class DealStatuses extends \HubletoCore\Core\Controller {

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => 'lead-statuses', 'content' => $this->translate('Deal Statuses') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/Deals/Views/DealStatuses.twig');
  }
}