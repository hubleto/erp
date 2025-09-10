<?php

namespace Hubleto\App\Community\Cloud\Controllers\Api;

use Hubleto\App\Community\Cloud\PremiumAccount;

class ActivatePremiumAccount extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    /** @var PremiumAccount */
    $premiumAccount = $this->getService(PremiumAccount::class);
    $premiumAccount->activatePremiumAccount();
    $this->router()->redirectTo('cloud');

    return [];
  }

}
