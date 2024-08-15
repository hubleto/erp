<?php

namespace CeremonyCrmApp\Modules\Core\Sandbox\Controllers;

class Companies extends \CeremonyCrmApp\Core\Controller {

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'sandbox', 'content' => $this->app->translate('Sandbox') ],
      [ 'url' => '', 'content' => $this->app->translate('Companies') ],
    ]);
  }

  public function prepareViewParams()
  {
    parent::prepareViewParams();
    
    $mCategory = new \CeremonyCrmApp\Modules\Core\Sandbox\Models\Category($this->app);
    $this->viewParams['categories'] = \ADIOS\Core\Helper::keyBy('id', $mCategory->eloquent->get()->toArray());
  }
  
}