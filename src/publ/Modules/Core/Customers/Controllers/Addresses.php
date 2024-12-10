<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Controllers;

class Addresses extends \CeremonyCrmApp\Core\Controller {

  public string $translationContext = 'mod.core.customers.controllers.addresses';

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
    $this->setView('@app/Modules/Core/Customers/Views/Addresses.twig');
  }

}