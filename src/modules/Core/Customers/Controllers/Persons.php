<?php

namespace CeremonyCrmMod\Core\Customers\Controllers;

class Persons extends \CeremonyCrmApp\Core\Controller {

  public string $translationContext = 'mod.core.customers.controllers.persons';

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'customers/companies', 'content' => $this->translate('Customers') ],
      [ 'url' => '', 'content' => $this->translate('Contact Persons') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@mod/Core/Customers/Views/Persons.twig');
  }

}