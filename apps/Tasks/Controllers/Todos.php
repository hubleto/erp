<?php

namespace Hubleto\App\Community\Tasks\Controllers;

class Todos extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Tasks/Todos.twig');
  }

}
