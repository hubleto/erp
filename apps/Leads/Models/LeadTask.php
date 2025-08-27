<?php

namespace HubletoApp\Community\Leads\Models;

use Hubleto\Framework\Db\Column\Lookup;
use HubletoApp\Community\Tasks\Models\Task;

class LeadTask extends \HubletoMain\Model
{
  public string $table = 'leads_tasks';
  public string $recordManagerClass = RecordManagers\LeadTask::class;

  public array $relations = [
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_lead', 'id' ],
    'TASK' => [ self::BELONGS_TO, Task::class, 'id_task', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_lead' => (new Lookup($this, $this->translate('Lead'), Lead::class))->setRequired(),
      'id_task' => (new Lookup($this, $this->translate('Task'), Task::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Add lead';
    return $description;
  }

}
