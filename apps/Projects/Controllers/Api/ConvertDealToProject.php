<?php

namespace HubletoApp\Community\Projects\Controllers\Api;

use Exception;
use HubletoApp\Community\Projects\Models\Project;
use HubletoApp\Community\Deals\Models\Deal;
use HubletoApp\Community\Pipeline\Models\Pipeline;

class ConvertDealToProject extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    if (!$this->main->isUrlParam("idDeal")) {
      return [
        "status" => "failed",
        "error" => "The deal for converting was not set"
      ];
    }

    $idDeal = $this->main->urlParamAsInteger("idDeal");

    $mDeal = $this->main->di->create(Deal::class);
    $mProject = $this->main->di->create(Project::class);
    $project = null;

    try {
      $deal = $mDeal->record->prepareReadQuery()->where($mDeal->table.".id", $idDeal)->first();
      if (!$deal) {
        throw new Exception("Deal was not found.");
      }

      $projectsCount = $mProject->record->where('id_deal', $deal->id)->count();

      $mPipeline = $this->main->di->create(Pipeline::class);
      list($defaultPipeline, $idPipeline, $idPipelineStep) = $mPipeline->getDefaultPipelineInfo(Pipeline::TYPE_PROJECT_MANAGEMENT);

      $project = $mProject->record->recordCreate([
        "id_deal" => $deal->id,
        "id_customer" => $deal->id_customer,
        "id_contact" => $deal->id_contact,
        "title" => $deal->title,
        "identifier" => $deal->identifier . ':' . ($projectsCount + 1),
        "id_main_developer" => $this->main->auth->getUserId(),
        "id_account_manager" => $this->main->auth->getUserId(),
        "id_pipeline" => $idPipeline,
        "id_pipeline_step" => $idPipelineStep,
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
