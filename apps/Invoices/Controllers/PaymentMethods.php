<?php

namespace Hubleto\App\Community\Invoices\Controllers;

class PaymentMethods extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => '', 'content' => $this->translate('Payment methods') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Invoices/PaymentMethods.twig');
  }

}
