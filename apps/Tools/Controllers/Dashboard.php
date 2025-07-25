<?php

namespace HubletoApp\Community\Tools\Controllers;

class Dashboard extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'tools', 'content' => $this->translate('Tools') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->viewParams['tools'] = $this->hubletoApp->getTools();
    $this->setView('@HubletoApp:Community:Tools/Dashboard.twig');
  }

}
