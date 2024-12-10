<?php

namespace CeremonyCrmApp\Modules\Core\Extensions\Controllers;

class Dashboard extends \CeremonyCrmApp\Core\Controller
{

  public string $translationContext = 'mod.core.extensions.controllers.dashboard';

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'extensions', 'content' => $this->translate('Extensions') ],
    ]);
  }
  
  public function prepareView(): void
  {
    parent::prepareView();
    $this->viewParams['extensions'] = $this->app->getExtensions();
    $this->setView('@app/Modules/Core/Extensions/Views/Dashboard.twig');
  }

}