<?php

namespace Hubleto\App\Community\Cloud\Controllers\Test;

use Hubleto\App\Community\Cloud\PremiumAccount;

class MakeRandomPayment extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    $premiumAccount = $this->getService(PremiumAccount::class);

    $amount = rand(15, 20) / 2;

    /** @var \Hubleto\App\Community\Cloud\Models\Payment */
    $mPayment = $this->getModel(\Hubleto\App\Community\Cloud\Models\Payment::class);
    $mPayment->addPayment(date('Y-m-d H:i:s'), $amount, 'TEST: random payment');

    $this->viewParams['amount'] = $amount;
    $premiumAccount->recalculateCredit();

    $this->setView('@Hubleto:App:Community:Cloud/Test/MakeRandomPayment.twig');
  }

}
