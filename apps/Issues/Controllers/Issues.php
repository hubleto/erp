<?php

namespace HubletoApp\Community\Issues\Controllers;

class Issues extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'issues', 'content' => 'Issues' ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@HubletoApp:Community:Issues/Issues.twig');
  }

}
