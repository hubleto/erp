<?php

namespace HubletoApp\Community\Cloud\Controllers\Test;

use HubletoApp\Community\Cloud\PremiumAccount;

class ClearCredit extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    $premiumAccount = $this->getService(PremiumAccount::class);

    $currentCredit = $premiumAccount->getCurrentCredit();

    $mPayment = $this->getModel(\HubletoApp\Community\Cloud\Models\Payment::class);
    $mPayment->record->recordCreate(['datetime_charged' => date('Y-m-d H:i:s'), 'amount' => -$currentCredit - 1]);

    $premiumAccount->recalculateCredit();

    $this->setView('@HubletoApp:Community:Cloud/Test/ClearCredit.twig');
  }

}
