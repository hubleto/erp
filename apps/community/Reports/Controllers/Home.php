<?php

namespace HubletoApp\Community\Reports\Controllers;

class Home extends \HubletoMain\Core\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    $reports = $this->main->reportManager->getReports();
    $this->viewParams['reports'] = $reports;

    $this->setView('@HubletoApp:Community:Reports/Home.twig');
  }

}