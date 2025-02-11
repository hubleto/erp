<?php

namespace HubletoApp\Community\Settings\Models;

use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Integer;
use \ADIOS\Core\Db\Column\Lookup;

class PipelineStep extends \HubletoMain\Core\Model
{
  public string $table = 'pipeline_steps';
  public string $eloquentClass = Eloquent\PipelineStep::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public array $relations = [
    'PIPELINE' => [ self::BELONGS_TO, Pipeline::class, 'id_pipeline', 'id' ]
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired(),
      'order' => (new Integer($this, $this->translate('Order')))->setRequired(),
      'id_pipeline' => (new Lookup($this, $this->translate("Pipeline"), Pipeline::class, 'CASCADE'))->setRequired(),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Pipeline Steps';
    $description->ui['addButtonText'] = 'Add Pipeline Step';
    $description->ui['showHeader'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }
}
