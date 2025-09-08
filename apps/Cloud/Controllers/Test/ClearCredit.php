<?php

namespace Hubleto\App\Community\Cloud\Controllers\Test;

use Hubleto\App\Community\Cloud\PremiumAccount;

class ClearCredit extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    $premiumAccount = $this->getService(PremiumAccount::class);

    $currentCredit = $premiumAccount->getCurrentCredit();

    $mPayment = $this->getModel(\Hubleto\App\Community\Cloud\Models\Payment::class);
    $mPayment->record->recordCreate(['datetime_charged' => date('Y-m-d H:i:s'), 'amount' => -$currentCredit - 1]);

    $premiumAccount->recalculateCredit();

    $this->setView('@Hubleto:App:Community:Cloud/Test/ClearCredit.twig');
  }

}
