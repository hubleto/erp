<?php

namespace Hubleto\App\Community\Workflow\Controllers;

use Hubleto\App\Community\Auth\Models\User;
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

    $fUser = $this->router()->urlParamAsInteger('fUser');

    /** @var User */
    $mUser = $this->getModel(User::class);
    $users = $mUser->record->where('is_active', true)->get();

    /** @var \Hubleto\App\Community\Workflow\Manager */
    $workflowManager = $this->getService(\Hubleto\App\Community\Workflow\Manager::class);
    $mWorkflow = $this->getModel(ModelWorkflow::class);

    $workflows = $mWorkflow->record->where('show_in_kanban', true)->get()?->toArray();
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

      $items = ($workflowLoader ? $workflowLoader->loadItems($idWorkflow, ['fUser' => $fUser]) : []);

      foreach ($items as $key => $item) {
        $items[$key]['_UID'] = md5($key . rand(0, 999999));
      }

      $this->viewParams["items"] = $items;
    }

    $this->viewParams["workflows"] = $workflows;
    $this->viewParams["users"] = $users;
    $this->viewParams["fUser"] = $fUser;

    $this->setView('@Hubleto:App:Community:Workflow/Workflow.twig');
  }

}
