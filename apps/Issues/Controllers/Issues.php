<?php

namespace Hubleto\App\Community\Issues\Controllers;

class Issues extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      // [ 'url' => 'issues', 'content' => 'Issues' ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Issues/Issues.twig');
  }

}
