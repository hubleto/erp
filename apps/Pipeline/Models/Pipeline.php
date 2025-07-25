<?php

namespace HubletoApp\Community\Pipeline\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Integer;

class Pipeline extends \Hubleto\Framework\Models\Model
{
  public const TYPE_DEAL_MANAGEMENT = 1;
  public const TYPE_PROJECT_MANAGEMENT = 2;
  public const TYPE_TASK_MANAGEMENT = 3;

  public const TYPE_ENUM_VALUES = [
    self::TYPE_DEAL_MANAGEMENT => 'deal management',
    self::TYPE_PROJECT_MANAGEMENT => 'project management',
    self::TYPE_TASK_MANAGEMENT => 'task management',
  ];

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
      'type' => (new Integer($this, $this->translate('Type')))->setEnumValues(self::TYPE_ENUM_VALUES)->setProperty('defaultVisibility', true),
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

  public function getDefaultPipelineInfo(int $type): array
  {
    $defaultPipeline = $this->record->where('type', $type)->with('STEPS')->first()?->toArray();

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

}
