<?php

namespace HubletoApp\Community\Support\Controllers;

class Dashboard extends \HubletoMain\Core\Controller {

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@app/Support/Views/Dashboard.twig');
  }

}