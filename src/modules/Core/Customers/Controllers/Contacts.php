<?php

namespace CeremonyCrmMod\Core\Customers\Controllers;

class Contacts extends \CeremonyCrmApp\Core\Controller {

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'customers/companies', 'content' => $this->translate('Customers') ],
      [ 'url' => '', 'content' => $this->translate('Contacts') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@mod/Core/Customers/Views/Contacts.twig');
  }

}