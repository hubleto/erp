<?php

namespace Hubleto\App\Community\Workflow\Controllers;

use Hubleto\App\Community\Deals\Models\Deal;
use Hubleto\App\Community\Projects\Models\Project;
use Hubleto\App\Community\Tasks\Models\Task;
use Hubleto\App\Community\Orders\Models\Order;
use Hubleto\App\Community\Workflow\Models\Workflow as ModelWorkflow;

class Workflow extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      // [ 'url' => '', 'content' => $this->translate('Workflow') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $fOwner = $this->router()->urlParamAsInteger('fOwner');

    /** @var \Hubleto\App\Community\Workflow\Manager */
    $workflowManager = $this->getService(\Hubleto\App\Community\Workflow\Manager::class);
    $mWorkflow = $this->getModel(ModelWorkflow::class);

    $workflows = $mWorkflow->record->get()?->toArray();
    if (!is_array($workflows)) $workflows = [];

    $idWorkflow = $this->router()->urlParamAsInteger('idWorkflow');

    $workflow = $mWorkflow->record
      ->where("id", $idWorkflow)
      ->with("STEPS")
      ->first()
    ;

    if ($workflow) {
      $workflowLoader = $workflowManager->getWorkflowLoaderForGroup($workflow->group);

      $this->viewParams["workflow"] = $workflow;
      $this->viewParams["items"] = ($workflowLoader ? $workflowLoader->loadItems($idWorkflow, ['fOwner' => $fOwner]) : []);
    }

    $this->viewParams["workflows"] = $workflows;

    $this->setView('@Hubleto:App:Community:Workflow/Workflow.twig');
  }

}
