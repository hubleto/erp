<?php

namespace Hubleto\App\Community\Projects\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\App\Community\Tasks\Models\Task;

class MilestoneTask extends \Hubleto\Erp\Model
{
  public string $table = 'projects_milestones_tasks';
  public string $recordManagerClass = RecordManagers\MilestoneTask::class;

  public array $relations = [
    'MILESTONE' => [ self::BELONGS_TO, Milestone::class, 'id_milestone', 'id' ],
    'TASK' => [ self::BELONGS_TO, Task::class, 'id_task', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_milestone' => (new Lookup($this, $this->translate('Milestone'), Milestone::class))->setRequired(),
      'id_task' => (new Lookup($this, $this->translate('Task'), Task::class))->setRequired()->setDefaultVisible(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = $this->translate('Assign task to milestone');
    $description->show(['header', 'fulltextSearch']);
    $description->hide(['footer']);
    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();

    $idTask = $this->router()->urlParamAsInteger('idTask');
    $description->defaultValues = ['id_task' => $idTask];
    return $description;
  }

}
