<?php

namespace HubletoApp\Community\Pipeline\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Integer;

class Pipeline extends \HubletoMain\Model
{
  public string $table = 'pipelines';
  public string $recordManagerClass = RecordManagers\Pipeline::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public array $relations = [
    'STEPS' => [ self::HAS_MANY, PipelineStep::class, 'id_pipeline', 'id' ]
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired()->setProperty('defaultVisibility', true),
      'description' => (new Varchar($this, $this->translate('Description')))->setProperty('defaultVisibility', true),
      'group' => (new Varchar($this, $this->translate('Group')))->setProperty('defaultVisibility', true)->setPredefinedValues([
        'deals',
        'orders',
        'projects',
        'tasks',
        'leads',
        'campaigns',
      ])->addIndex('INDEX `group` (`group`)'),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    // $description->ui['title'] = 'Pipelines';
    $description->ui['addButtonText'] = 'Add Pipeline';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

  public function getDefaultPipelineInGroup(string $group): array
  {
    $defaultPipeline = $this->record->where('group', $group)->with('STEPS')->first()?->toArray();

    $idPipeline = 0;
    $idPipelineStep = 0;
    if (is_array($defaultPipeline)) {
      $idPipeline = $defaultPipeline['id'] ?? 0;
      if (is_array($defaultPipeline['STEPS'])) {
        $idPipelineStep = reset($defaultPipeline['STEPS'])['id'] ?? 0;
      }
    }

    return [$defaultPipeline, $idPipeline, $idPipelineStep];
  }

  public static function buildTableDefaultFilterForPipelineSteps(\HubletoMain\Model $model, string $title): array
  {
    $fPipelineSteps = [
      'title' => $title,
      'type' => 'multipleSelectButtons', 
      'options' => [],
      'colors' => [],
    ];
    $pipelineStepsUsed = $model->record->with('PIPELINE_STEP')->groupBy('id_pipeline_step')->get();
    if ($pipelineStepsUsed) {
      foreach ($pipelineStepsUsed as $step) {
        if ($step->PIPELINE_STEP) {
          $fPipelineSteps['options'][$step->PIPELINE_STEP->id] = $step->PIPELINE_STEP->name;
          $fPipelineSteps['colors'][$step->PIPELINE_STEP->id] = $step->PIPELINE_STEP->color;
        }
      }
    }
    return $fPipelineSteps;
  }

}
