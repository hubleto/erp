<?php

namespace Hubleto\App\Community\Projects\Controllers;

class Milestones extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Projects/Milestones.twig');
  }

}
