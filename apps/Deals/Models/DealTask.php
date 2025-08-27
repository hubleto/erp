<?php

namespace HubletoApp\Community\Deals\Models;

use Hubleto\Framework\Db\Column\Lookup;
use HubletoApp\Community\Tasks\Models\Task;

class DealTask extends \HubletoMain\Model
{
  public string $table = 'deals_tasks';
  public string $recordManagerClass = RecordManagers\DealTask::class;

  public array $relations = [
    'DEAL' => [ self::BELONGS_TO, Deal::class, 'id_deal', 'id' ],
    'TASK' => [ self::BELONGS_TO, Task::class, 'id_task', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_deal' => (new Lookup($this, $this->translate('Deal'), Deal::class))->setRequired(),
      'id_task' => (new Lookup($this, $this->translate('Task'), Task::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Add deal';
    return $description;
  }

}
