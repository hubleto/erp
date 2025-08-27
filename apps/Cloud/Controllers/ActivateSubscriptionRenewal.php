<?php

namespace HubletoApp\Community\Cloud\Controllers;

use HubletoApp\Community\Cloud\PremiumAccount;

class ActivateSubscriptionRenewal extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    $premiumAccount = $this->getService(PremiumAccount::class);
    $premiumAccount->activateSubscriptionRenewal();

    $this->getRouter()->redirectTo('cloud');
  }

}
