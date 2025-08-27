<?php

namespace Hubleto\App\Community\Cloud\Controllers;

use Hubleto\App\Community\Cloud\PremiumAccount;

class DeactivateSubscriptionRenewal extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $premiumAccount = $this->getService(PremiumAccount::class);
    $premiumAccount->deactivateSubscriptionRenewal();
    $this->getRouter()->redirectTo('cloud');
  }

}
