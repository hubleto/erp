<?php

namespace Hubleto\App\Community\Pipeline\Controllers;

class PipelineHistory extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'pipeline', 'content' => $this->translate('Pipeline') ],
      [ 'url' => 'history', 'content' => $this->translate('History') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Pipeline/PipelineHistory.twig');
  }
}
