<?php

namespace HubletoApp\Community\Pipeline\Models;

use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Datetime;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;

class PipelineHistory extends \HubletoMain\Model
{
  public string $table = 'pipeline_history';
  public string $recordManagerClass = RecordManagers\PipelineHistory::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'model' => (new Varchar($this, $this->translate('Model')))->addIndex('INDEX `model` (`model`)'),
      'record_id' => (new Integer($this, $this->translate('Record Id')))->setRequired(),
      'datetime_change' => (new Datetime($this, $this->translate('Changed')))->setRequired(),
      'id_pipeline' => (new Lookup($this, $this->translate("Pipeline"), Pipeline::class))->setRequired(),
      'id_pipeline_step' => (new Lookup($this, $this->translate("Pipeline Step"), PipelineStep::class))->setRequired(),
    ]);
  }

}
