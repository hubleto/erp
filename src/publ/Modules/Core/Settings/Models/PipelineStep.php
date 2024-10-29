<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Models;

class PipelineStep extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'pipeline_steps';
  public string $eloquentClass = Eloquent\PipelineStep::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public array $relations = [
    'PIPELINE' => [ self::BELONGS_TO, Pipeline::class, 'id_pipeline', 'id' ]
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'name' => [
        'type' => 'varchar',
        'title' => 'Name',
        'required' => true,
      ],
      'order' => [
        'type' => 'int',
        'title' => 'Order',
        'required' => true,
      ],
      'id_pipeline' => [
        'type' => 'lookup',
        'title' => 'Company',
        'model' => 'CeremonyCrmApp/Modules/Core/Settings/Models/Pipeline',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = 'Pipeline Steps';
    $description['ui']['addButtonText'] = 'Add Pipeline Step';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;

    return $description;
  }
}
