<?php

namespace Hubleto\App\Community\Pipeline\Controllers;

use Hubleto\App\Community\Deals\Models\Deal;
use Hubleto\App\Community\Projects\Models\Project;
use Hubleto\App\Community\Tasks\Models\Task;
use Hubleto\App\Community\Orders\Models\Order;
use Hubleto\App\Community\Pipeline\Models\Pipeline as ModelPipeline;

class Pipeline extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => '', 'content' => $this->translate('Pipeline') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $fOwner = $this->getRouter()->urlParamAsInteger('fOwner');

    /** @var \Hubleto\App\Community\Pipeline\Manager */
    $pipelineManager = $this->getService(\Hubleto\App\Community\Pipeline\Manager::class);
    $mPipeline = $this->getModel(ModelPipeline::class);

    $pipelines = $mPipeline->record->get()?->toArray();
    if (!is_array($pipelines)) $pipelines = [];

    $idPipeline = $this->getRouter()->urlParamAsInteger('idPipeline');

    $pipeline = $mPipeline->record
      ->where("id", $idPipeline)
      ->with("STEPS")
      ->first()
    ;

    if ($pipeline) {
      $pipelineLoader = $pipelineManager->getPipelineLoaderForGroup($pipeline->group);

      $this->viewParams["pipeline"] = $pipeline;
      $this->viewParams["items"] = $pipelineLoader->loadItems($idPipeline, ['fOwner' => $fOwner]);
    }

    $this->viewParams["pipelines"] = $pipelines;

    $this->setView('@Hubleto:App:Community:Pipeline/Pipeline.twig');
  }

}
