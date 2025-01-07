<?php

namespace HubletoApp\Billing\Controllers;

class BillingAccounts extends \HubletoCore\Core\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'customers', 'content' => $this->translate('Customers') ],
      [ 'url' => '', 'content' => $this->translate('Billing Accounts') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/Billing/Views/BillingAccounts.twig');
  }

}