<?php

namespace HubletoApp\Community\Cloud\Controllers\Test;

class MakeRandomPayment extends \HubletoMain\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    $amount = rand(15, 20) / 2;

    $mPayment = $this->main->di->create(\HubletoApp\Community\Cloud\Models\Payment::class);
    $mPayment->addPayment(date('Y-m-d H:i:s'), $amount, 'TEST: random payment');

    $this->viewParams['amount'] = $amount;
    $this->hubletoApp->recalculateCredit();

    $this->setView('@HubletoApp:Community:Cloud/Test/MakeRandomPayment.twig');
  }

}
