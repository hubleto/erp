<?php

namespace Hubleto\App\Community\Workflow\Models;


use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Datetime;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;



class WorkflowHistory extends \Hubleto\Erp\Model
{
  public string $table = 'workflow_history';
  public string $recordManagerClass = RecordManagers\WorkflowHistory::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id';

  public array $relations = [
    'USER' => [ self::BELONGS_TO, \Hubleto\Framework\Models\User::class, 'id_user', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'model' => (new Varchar($this, $this->translate('Model')))->addIndex('INDEX `model` (`model`)'),
      'record_id' => (new Integer($this, $this->translate('Record Id')))->setRequired(),
      'datetime_change' => (new Datetime($this, $this->translate('Changed')))->setRequired(),
      'id_user' => (new Lookup($this, $this->translate('User'), \Hubleto\Framework\Models\User::class))->setReactComponent('InputUserSelect')->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId()),
      'id_workflow' => (new Lookup($this, $this->translate("Workflow"), Workflow::class))->setRequired(),
      'id_workflow_step' => (new Lookup($this, $this->translate("Workflow Step"), WorkflowStep::class))->setRequired(),
    ]);
  }

}
