<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Models;

class Pipeline extends \CeremonyCrmApp\Core\Model
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
        'title' => 'Name',
        'required' => true,
      ],
      'description' => [
        'type' => 'varchar',
        'title' => 'Description',
        'required' => false,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = 'Pipelines';
    $description['ui']['addButtonText'] = 'Add Pipeline';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    return $description;
  }

  public function formDescribe(array $description = []): array
  {
    $description = parent::formDescribe();
    $description['includeRelations'] = ['PIPELINE_STEPS'];
    return $description;
  }

}
