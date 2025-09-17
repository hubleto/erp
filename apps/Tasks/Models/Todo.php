<?php

namespace Hubleto\App\Community\Tasks\Models;


use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Color;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\File;
use Hubleto\Framework\Db\Column\Image;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Json;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Password;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Virtual;

use Hubleto\App\Community\Workflow\Models\Workflow;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;
use Hubleto\App\Community\Contacts\Models\Contact;
use Hubleto\App\Community\Customers\Models\Customer;

class Todo extends \Hubleto\Erp\Model
{
  public string $table = 'tasks_todo';
  public string $recordManagerClass = RecordManagers\Todo::class;
  public ?string $lookupSqlValue = '{%TABLE%}.todo';

  public array $relations = [
    'RESPONSIBLE' => [ self::BELONGS_TO, \Hubleto\Framework\Models\User::class, 'id_responsible', 'id' ],
    'TASK' => [ self::BELONGS_TO, Task::class, 'id_task', 'id' ],
  ];

  /**
   * [Description for describeColumns]
   *
   * @return array
   * 
   */
  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'todo' => (new Varchar($this, $this->translate('To do')))->setProperty('defaultVisibility', true),
      'id_task' => (new Lookup($this, $this->translate('Task'), Task::class))->setProperty('defaultVisibility', true)->setRequired(),
      'id_responsible' => (new Lookup($this, $this->translate('Responsible'), \Hubleto\Framework\Models\User::class))->setReactComponent('InputUserSelect')->setProperty('defaultVisibility', true)
        ->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId())
      ,
      'is_closed' => (new Boolean($this, $this->translate('Closed')))->setDefaultValue(false),
      'date_deadline' => (new Date($this, $this->translate('Deadline')))->setDefaultValue(date("Y-m-d")),
    ]);
  }

  /**
   * [Description for describeTable]
   *
   * @return \Hubleto\Framework\Description\Table
   * 
   */
  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add Task';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

}
