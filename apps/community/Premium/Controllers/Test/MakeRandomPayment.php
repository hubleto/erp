<?php

namespace HubletoApp\Community\Premium\Controllers\Test;

class MakeRandomPayment extends \HubletoMain\Core\Controllers\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    $mPayment = new \HubletoApp\Community\Premium\Models\Payment($this->main);
    $amount = rand(5, 10) * 10;

    $mPayment->record->recordCreate(['datetime_charged' => date('Y-m-d H:i:s'), 'amount' => $amount]);

    $this->viewParams['amount'] = $amount;
    $this->hubletoApp->recalculateCredit();

    $this->setView('@HubletoApp:Community:Premium/Test/MakeRandomPayment.twig');
  }

}