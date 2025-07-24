<?php

namespace HubletoApp\Community\Cloud\Controllers;

class BillingAccounts extends \Hubleto\Framework\Controllers\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Cloud/BillingAccounts.twig');
  }

}
