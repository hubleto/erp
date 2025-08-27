<?php

namespace HubletoApp\Community\Projects\Models;

use Hubleto\Framework\Db\Column\Lookup;
use HubletoApp\Community\Tasks\Models\Task;

class ProjectTask extends \HubletoMain\Model
{
  public string $table = 'projects_tasks';
  public string $recordManagerClass = RecordManagers\ProjectTask::class;

  public array $relations = [
    'PROJECT' => [ self::BELONGS_TO, Project::class, 'id_project', 'id' ],
    'TASK' => [ self::BELONGS_TO, Task::class, 'id_task', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_project' => (new Lookup($this, $this->translate('Project'), Project::class))->setRequired(),
      'id_task' => (new Lookup($this, $this->translate('Task'), Task::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Add project';
    return $description;
  }

}
