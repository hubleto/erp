<?php

namespace HubletoApp\Community\Customers\Controllers;

class Customers extends \HubletoMain\Core\Controllers\Controller {

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'customers', 'content' => $this->translate('Customers') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Customers/Customers.twig');
  }

}