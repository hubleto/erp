<?php

namespace Hubleto\App\Community\Workflow\Controllers;

class WorkflowHistory extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'workflow', 'content' => $this->translate('Workflow') ],
      [ 'url' => 'history', 'content' => $this->translate('History') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Workflow/WorkflowHistory.twig');
  }
}
