<?php

namespace HubletoApp\Community\Invoices\Controllers;

class Invoices extends \HubletoMain\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => '', 'content' => $this->translate('Invoices') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Invoices/Invoices.twig');
  }

}