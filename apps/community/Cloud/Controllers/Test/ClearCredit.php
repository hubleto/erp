<?php

namespace HubletoApp\Community\Cloud\Controllers\Test;

class ClearCredit extends \HubletoMain\Core\Controllers\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    $currentCredit = $this->hubletoApp->getCurrentCredit();

    $mPayment = new \HubletoApp\Community\Cloud\Models\Payment($this->main);
    $mPayment->record->recordCreate(['datetime_charged' => date('Y-m-d H:i:s'), 'amount' => -$currentCredit - 1]);

    $this->hubletoApp->recalculateCredit();

    $this->setView('@HubletoApp:Community:Cloud/Test/ClearCredit.twig');
  }

}