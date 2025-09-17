<?php

namespace Hubleto\App\Community\Projects\Controllers\Api;

use Exception;
use Hubleto\App\Community\Auth\AuthProvider;
use Hubleto\App\Community\Projects\Models\Project;
use Hubleto\App\Community\Deals\Models\Deal;
use Hubleto\App\Community\Workflow\Models\Workflow;

class ConvertDealToProject extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    if (!$this->router()->isUrlParam("idDeal")) {
      return [
        "status" => "failed",
        "error" => "The deal for converting was not set"
      ];
    }

    $idDeal = $this->router()->urlParamAsInteger("idDeal");

    $mDeal = $this->getService(Deal::class);
    $mProject = $this->getService(Project::class);
    $project = null;

    try {
      $deal = $mDeal->record->prepareReadQuery()->where($mDeal->table.".id", $idDeal)->first();
      if (!$deal) {
        throw new Exception("Deal was not found.");
      }

      $projectsCount = $mProject->record->where('id_deal', $deal->id)->count();

      $mWorkflow = $this->getService(Workflow::class);
      list($defaultWorkflow, $idWorkflow, $idWorkflowStep) = $mWorkflow->getDefaultWorkflowInGroup('projects');

      $project = $mProject->record->recordCreate([
        "id_deal" => $deal->id,
        "id_customer" => $deal->id_customer,
        "id_contact" => $deal->id_contact,
        "title" => $deal->title,
        "identifier" => $deal->identifier . ':' . ($projectsCount + 1),
        "id_main_developer" => $this->getService(AuthProvider::class)->getUserId(),
        "id_account_manager" => $this->getService(AuthProvider::class)->getUserId(),
        "id_workflow" => $idWorkflow,
        "id_workflow_step" => $idWorkflowStep,
        "is_closed" => false,
        "date_created" => date("Y-m-d H:i:s"),
      ]);
    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    return [
      "status" => "success",
      "idProject" => $project['id'],
      "title" => str_replace(" ", "+", (string) $project['title'])
    ];
  }

}
