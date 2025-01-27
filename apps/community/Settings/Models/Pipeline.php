<?php

namespace HubletoApp\Community\Settings\Models;

class Pipeline extends \HubletoMain\Core\Model
{
  public string $table = 'pipelines';
  public string $eloquentClass = Eloquent\Pipeline::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public array $relations = [
    'PIPELINE_STEPS' => [ self::HAS_MANY, PipelineStep::class, 'id_pipeline', 'id' ]
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'name' => [
        'type' => 'varchar',
        'title' => $this->translate('Name'),
        'required' => true,
      ],
      'description' => [
        'type' => 'varchar',
        'title' => $this->translate('Description'),
        'required' => false,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe($description);

    if (is_array($description['ui'])) {
      $description['ui']['title'] = 'Pipelines';
      $description['ui']['addButtonText'] = 'Add Pipeline';
      $description['ui']['showHeader'] = true;
      $description['ui']['showFooter'] = false;
    }

    return $description;
  }

  public function formDescribe(array $description = []): array
  {
    $description = parent::formDescribe();
    $description['includeRelations'] = ['PIPELINE_STEPS'];
    return $description;
  }

}
