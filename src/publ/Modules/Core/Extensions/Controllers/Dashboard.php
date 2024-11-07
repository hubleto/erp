<?php

namespace CeremonyCrmApp\Modules\Core\Extensions\Controllers;

class Dashboard extends \CeremonyCrmApp\Core\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'extensions', 'content' => $this->app->translate('Extensions') ],
    ]);
  }
  
  public function prepareViewParams()
  {
    parent::prepareViewParams();

    $this->viewParams['extensions'] = $this->app->getExtensions();
  }

}