<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Controllers;

class Persons extends \CeremonyCrmApp\Core\Controller {
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'customers/companies', 'content' => $this->app->translate('Customers') ],
      [ 'url' => '', 'content' => $this->app->translate('Contact Persons') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/Modules/Core/Customers/Views/Persons.twig');
  }

}