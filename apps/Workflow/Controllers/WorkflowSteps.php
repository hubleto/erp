<?php

namespace Hubleto\App\Community\Workflow\Controllers;

class WorkflowSteps extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => '', 'content' => $this->translate('Workflows') ],
      [ 'url' => '', 'content' => $this->translate('Steps') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->setView('@Hubleto:App:Community:Workflow/WorkflowSteps.twig');
  }
}
