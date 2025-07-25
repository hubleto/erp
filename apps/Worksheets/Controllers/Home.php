<?php

namespace HubletoApp\Community\Worksheets\Controllers;

class Home extends \HubletoMain\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Worksheets/Home.twig');
  }

}
