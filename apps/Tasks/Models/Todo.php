<?php

namespace Hubleto\App\Community\Tasks\Models;


use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;

use Hubleto\App\Community\Auth\Models\User;

class Todo extends \Hubleto\Erp\Model
{
  public string $table = 'tasks_todo';
  public string $recordManagerClass = RecordManagers\Todo::class;
  public ?string $lookupSqlValue = '{%TABLE%}.todo';

  public array $relations = [
    'RESPONSIBLE' => [ self::BELONGS_TO, User::class, 'id_responsible', 'id' ],
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
      'todo' => (new Varchar($this, $this->translate('To do')))->setDefaultVisible(),
      'id_task' => (new Lookup($this, $this->translate('Task'), Task::class))->setDefaultVisible()->setRequired(),
      'id_responsible' => (new Lookup($this, $this->translate('Responsible'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()
        ->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId())
      ,
      'is_closed' => (new Boolean($this, $this->translate('Closed')))->setDefaultValue(false),
      'date_deadline' => (new Date($this, $this->translate('Deadline')))->setDefaultVisible()->setDefaultValue(date("Y-m-d")),
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
    $description->ui['addButtonText'] = $this->translate('Add Todo');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    $description->addFilter(
      'fTodoClosed',
      [
        'title' => $this->translate('Open / Closed'),
        'options' => [
          0 => $this->translate('Open'),
          1 => $this->translate('Closed'),
          2 => $this->translate('All'),
        ],
        'default' => 0,
      ]
    );

    $fUserOptions = [];
    foreach ($this->getModel(User::class)->record->where('is_active', true)->get() as $value) {
      $fUserOptions[$value->id] = $value->nick;
    }
    $description->addFilter('fResponsible', [
      'title' => $this->translate('Responsible'),
      'type' => 'multipleSelectButtons',
      'options' => $fUserOptions,
    ]);

    return $description;
  }

}
