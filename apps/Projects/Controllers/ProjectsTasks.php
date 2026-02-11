<?php

namespace Hubleto\App\Community\Projects\Controllers;

class ProjectsTasks extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Projects/ProjectsTasks.twig');
  }

}
