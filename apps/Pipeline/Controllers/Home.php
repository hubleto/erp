<?php

namespace Hubleto\App\Community\Pipeline\Controllers;

class Home extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'pipeline', 'content' => $this->translate('Pipeline') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Pipeline/Home.twig');
  }
}
