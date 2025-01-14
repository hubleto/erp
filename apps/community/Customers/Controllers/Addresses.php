<?php

namespace HubletoApp\Community\Customers\Controllers;

class Addresses extends \HubletoMain\Core\Controller {

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'customers/companies', 'content' => $this->translate('Customers') ],
      [ 'url' => '', 'content' => $this->translate('Addresses') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/community/Customers/Views/Addresses.twig');
  }

}