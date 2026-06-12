<?php

namespace Hubleto\App\Community\Cloud\Controllers;

class Credit extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    $this->setView('@Hubleto:App:Community:Cloud/Credit.twig');
  }

}
