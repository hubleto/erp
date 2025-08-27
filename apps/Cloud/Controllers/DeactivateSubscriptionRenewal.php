<?php

namespace HubletoApp\Community\Cloud\Controllers;

use HubletoApp\Community\Cloud\PremiumAccount;

class DeactivateSubscriptionRenewal extends \HubletoMain\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $premiumAccount = $this->getService(PremiumAccount::class);
    $premiumAccount->deactivateSubscriptionRenewal();
    $this->getRouter()->redirectTo('cloud');
  }

}
