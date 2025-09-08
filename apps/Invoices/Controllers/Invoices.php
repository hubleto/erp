<?php

namespace Hubleto\App\Community\Invoices\Controllers;

class Invoices extends \Hubleto\Erp\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => '', 'content' => $this->translate('Invoices') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Invoices/Invoices.twig');
  }

}