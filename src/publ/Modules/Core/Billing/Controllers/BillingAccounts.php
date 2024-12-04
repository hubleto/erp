<?php

namespace CeremonyCrmApp\Modules\Core\Billing\Controllers;

class BillingAccounts extends \CeremonyCrmApp\Core\Controller {
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'customers', 'content' => $this->app->translate('Customers') ],
      [ 'url' => '', 'content' => $this->app->translate('Billing Accounts') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/Modules/Core/Billing/Views/BillingAccounts.twig');
  }

}