<?php

namespace HubletoApp\Community\Cloud\Controllers;

class PaymentsAndInvoices extends \HubletoMain\Core\Controllers\Controller {

  public function prepareView(): void
  {
    parent::prepareView();
    $mPayment = new \HubletoApp\Community\Cloud\Models\Payment($this->main);
    $this->viewParams['payments'] = $mPayment->record->get()?->toArray();
    $this->setView('@HubletoApp:Community:Cloud/PaymentsAndInvoices.twig');
  }

}