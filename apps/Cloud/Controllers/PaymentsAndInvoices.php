<?php

namespace Hubleto\App\Community\Cloud\Controllers;

class PaymentsAndInvoices extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    /** @var Hubleto\App\Community\Cloud\Models\Payment */
    $mPayment = $this->getModel(\Hubleto\App\Community\Cloud\Models\Payment::class);

    $payments = $mPayment->record->get()?->toArray();
    foreach ($payments as $key => $payment) {
      $payments[$key]['_ENUM[type]'] = $mPayment::TYPE_ENUM_VALUES[$payment['type']] ?? '';
      $payments[$key]['_ENUM[type_background_css_class]'] = $mPayment::TYPE_BACKGROUND_CSS_CLASSES[$payment['type']] ?? '';
    }
    $this->viewParams['payments'] = $payments;
    $this->setView('@Hubleto:App:Community:Cloud/PaymentsAndInvoices.twig');
  }

}
