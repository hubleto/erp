<?php

namespace Hubleto\App\Community\Cloud\Controllers\Api;

use Hubleto\App\Community\Cloud\PremiumAccount;

class ActivatePremiumAccount extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $premiumAccount = $this->getService(PremiumAccount::class);
    $premiumAccount->activatePremiumAccount();
    $this->getRouter()->redirectTo('cloud');
  }

}
