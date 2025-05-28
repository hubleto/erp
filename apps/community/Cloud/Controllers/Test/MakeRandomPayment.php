<?php

namespace HubletoApp\Community\Cloud\Controllers\Test;

class MakeRandomPayment extends \HubletoMain\Core\Controllers\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    $mPayment = new \HubletoApp\Community\Cloud\Models\Payment($this->main);
    $amount = rand(15, 20) / 2;

    $mPayment->record->recordCreate([
      'datetime_charged' => date('Y-m-d H:i:s'),
      'full_amount' => $amount,
      'notes' => 'TEST: random payment',
    ]);

    $this->viewParams['amount'] = $amount;
    $this->hubletoApp->recalculateCredit();

    $this->setView('@HubletoApp:Community:Cloud/Test/MakeRandomPayment.twig');
  }

}