<?php

namespace HubletoApp\Community\Cloud\Controllers\Test;

class MakeRandomPayment extends \HubletoMain\Core\Controllers\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    $amount = rand(15, 20) / 2;

    $mPayment = new \HubletoApp\Community\Cloud\Models\Payment($this->main);
    $mPayment->addPayment(date('Y-m-d H:i:s'), $amount, 'TEST: random payment');

    $this->viewParams['amount'] = $amount;
    $this->hubletoApp->recalculateCredit();

    $this->setView('@HubletoApp:Community:Cloud/Test/MakeRandomPayment.twig');
  }

}