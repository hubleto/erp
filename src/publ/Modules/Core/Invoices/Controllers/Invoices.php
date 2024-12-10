<?php

namespace CeremonyCrmApp\Modules\Core\Invoices\Controllers;

class Invoices extends \CeremonyCrmApp\Core\Controller {

  public string $translationContext = 'mod.core.invoices.controllers.invoices';

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => '', 'content' => $this->translate('Invoices') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/Modules/Core/Invoices/Views/Invoices.twig');
  }

}