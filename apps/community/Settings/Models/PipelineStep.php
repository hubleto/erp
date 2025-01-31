<?php

namespace HubletoApp\Community\Settings\Models;

class PipelineStep extends \HubletoMain\Core\Model
{
  public string $table = 'pipeline_steps';
  public string $eloquentClass = Eloquent\PipelineStep::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public array $relations = [
    'PIPELINE' => [ self::BELONGS_TO, Pipeline::class, 'id_pipeline', 'id' ]
  ];

  public function columnsLegacy(array $columns = []): array
  {
    return parent::columnsLegacy(array_merge($columns, [
      'name' => [
        'type' => 'varchar',
        'title' => $this->translate('Name'),
        'required' => true,
      ],
      'order' => [
        'type' => 'int',
        'title' => $this->translate('Order'),
        'required' => true,
      ],
      'id_pipeline' => [
        'type' => 'lookup',
        'title' => $this->translate('Company'),
        'model' => Pipeline::class,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe($description);

    if (is_array($description['ui'])) {
      $description['ui']['title'] = 'Pipeline Steps';
      $description['ui']['addButtonText'] = 'Add Pipeline Step';
      $description['ui']['showHeader'] = true;
      $description['ui']['showFooter'] = false;
    }

    return $description;
  }
}
