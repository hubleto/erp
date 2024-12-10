<?php

namespace CeremonyCrmApp\Modules\Core\Services\Controllers;

class Services extends \CeremonyCrmApp\Core\Controller {

  public string $translationContext = 'mod.core.services.controllers.services';

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => '', 'content' => $this->translate('Services') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/Modules/Core/Services/Views/Services.twig');
  }

}