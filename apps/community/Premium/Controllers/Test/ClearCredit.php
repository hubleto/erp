<?php

namespace HubletoApp\Community\Premium\Controllers\Test;

class ClearCredit extends \HubletoMain\Core\Controllers\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    $currentCredit = $this->hubletoApp->getCurrentCredit();

    $mPayment = new \HubletoApp\Community\Premium\Models\Payment($this->main);
    $mPayment->record->recordCreate(['datetime_charged' => date('Y-m-d H:i:s'), 'amount' => -$currentCredit - 1]);

    $this->hubletoApp->recalculateCredit();

    $this->setView('@HubletoApp:Community:Premium/Test/ClearCredit.twig');
  }

}