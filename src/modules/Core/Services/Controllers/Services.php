<?php

namespace CeremonyCrmMod\Core\Services\Controllers;

class Services extends \CeremonyCrmApp\Core\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => '', 'content' => $this->translate('Services') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@mod/Core/Services/Views/Services.twig');
  }

}