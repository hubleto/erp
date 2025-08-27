<?php

namespace Hubleto\App\Community\Pipeline\Controllers;

class Pipelines extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
      [ 'url' => '', 'content' => $this->translate('Pipelines') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Pipeline/Pipelines.twig');
  }
}
