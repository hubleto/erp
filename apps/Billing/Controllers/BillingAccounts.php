<?php

namespace HubletoApp\Community\Billing\Controllers;

class BillingAccounts extends \HubletoMain\Controller
{
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
    $this->setView('@HubletoApp:Community:Billing/BillingAccounts.twig');
  }

}
