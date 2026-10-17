<?php

namespace Hubleto\App\Community\Workflow\Controllers;

use Hubleto\App\Community\Workflow\Models\Workflow;

class Home extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    $mWorkflow = $this->getModel(Workflow::class);
    $this->viewParams['workflows'] = $mWorkflow->record->where('show_in_kanban', true)->orderBy('order')->get();

    $this->setView('@Hubleto:App:Community:Workflow/Home.twig');
  }
}
