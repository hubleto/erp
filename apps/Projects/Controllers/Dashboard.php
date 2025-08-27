<?php

namespace HubletoApp\Community\Projects\Controllers;

class Dashboard extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'projects', 'content' => 'Projects' ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->viewParams['now'] = date('Y-m-d H:i:s');
    $this->setView('@HubletoApp:Community:Projects/Dashboard.twig');
  }

}
