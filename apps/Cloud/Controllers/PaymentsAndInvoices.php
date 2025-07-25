<?php

namespace HubletoApp\Community\Cloud\Controllers;

class PaymentsAndInvoices extends \HubletoMain\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $mPayment = $this->main->di->create(\HubletoApp\Community\Cloud\Models\Payment::class);
    $payments = $mPayment->record->get()?->toArray();
    foreach ($payments as $key => $payment) {
      $payments[$key]['_ENUM[type]'] = $mPayment::TYPE_ENUM_VALUES[$payment['type']] ?? '';
      $payments[$key]['_ENUM[type_background_css_class]'] = $mPayment::TYPE_BACKGROUND_CSS_CLASSES[$payment['type']] ?? '';
    }
    $this->viewParams['payments'] = $payments;
    $this->setView('@HubletoApp:Community:Cloud/PaymentsAndInvoices.twig');
  }

}
