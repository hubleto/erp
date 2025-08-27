<?php

namespace HubletoApp\Community\Cloud\Controllers\Test;

use HubletoApp\Community\Cloud\PremiumAccount;

class MakeRandomPayment extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    $premiumAccount = $this->getService(PremiumAccount::class);

    $amount = rand(15, 20) / 2;

    /** @var \HubletoApp\Community\Cloud\Models\Payment */
    $mPayment = $this->getModel(\HubletoApp\Community\Cloud\Models\Payment::class);
    $mPayment->addPayment(date('Y-m-d H:i:s'), $amount, 'TEST: random payment');

    $this->viewParams['amount'] = $amount;
    $premiumAccount->recalculateCredit();

    $this->setView('@HubletoApp:Community:Cloud/Test/MakeRandomPayment.twig');
  }

}
