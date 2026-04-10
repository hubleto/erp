<?php

namespace Hubleto\App\Community\Issues\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\App\Community\Tasks\Models\Task;

class IssueTask extends \Hubleto\Erp\Model
{
  public string $table = 'issues_tasks';
  public string $recordManagerClass = RecordManagers\IssueTask::class;

  public array $relations = [
    'ISSUE' => [ self::BELONGS_TO, Issue::class, 'id_issue', 'id' ],
    'TASK' => [ self::BELONGS_TO, Task::class, 'id_task', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_issue' => (new Lookup($this, $this->translate('Issue'), Issue::class))->setRequired()->setDefaultVisible(),
      'id_task' => (new Lookup($this, $this->translate('Task'), Task::class))->setRequired()->setDefaultVisible(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = $this->translate('Assign task to issue');
    $description->show(['header', 'columnSearch', 'fulltextSearch']);
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
