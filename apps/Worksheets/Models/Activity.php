<?php

namespace Hubleto\App\Community\Worksheets\Models;


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

use Hubleto\App\Community\Tasks\Models\Task;
use Hubleto\App\Community\Auth\Models\User;

class Activity extends \Hubleto\Erp\Model
{
  public string $table = 'worksheet_activities';
  public string $recordManagerClass = RecordManagers\Activity::class;
  public ?string $lookupSqlValue = 'concat("Activity #", {%TABLE%}.id)';

  public array $relations = [
    'WORKER' => [ self::BELONGS_TO, User::class, 'id_worker', 'id' ],
    'TASK' => [ self::BELONGS_TO, Task::class, 'id_task', 'id' ],
    'TYPE' => [ self::BELONGS_TO, ActivityType::class, 'id_type', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_worker' => (new Lookup($this, $this->translate('Worker'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId())->setRequired(),
      'id_task' => (new Lookup($this, $this->translate('Task'), Task::class))->setDefaultVisible()->setRequired(),
      'id_type' => (new Lookup($this, $this->translate('Type'), ActivityType::class))->setDefaultVisible(),
      'date_worked' => (new Date($this, $this->translate('Day')))->setDefaultVisible()->setDefaultValue(date("Y-m-d")),
      'worked_hours' => (new Decimal($this, $this->translate('Worked hours')))->setDefaultVisible()->setDecimals(2)->setUnit('hours')->setStep(0.25),
      'description' => (new Text($this, $this->translate('Description')))->setDefaultVisible(),
      'is_approved' => (new Boolean($this, $this->translate('Approved')))->setDefaultVisible(),
      'is_chargeable' => (new Boolean($this, $this->translate('Is chargeable')))->setDefaultValue(true),
      'datetime_created' => (new DateTime($this, $this->translate('Created')))->setDefaultValue(date("Y-m-d H:i:s"))->setReadonly(true),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    switch ($this->router()->urlParamAsString('view')) {
      case 'briefOverview':
        $description->showOnlyColumns(['identifier', 'title', 'id_developer', 'virt_worked']);
      break;
      default:
        $description->ui['addButtonText'] = 'Add Activity';
        $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
        // $description->hide(['footer']);
      break;
    }

    $description->addFilter('fPeriod', [
      'title' => $this->translate('Period'),
      'options' => [
        'all' => $this->translate('All'),
        'today' => $this->translate('Today'),
        'yesterday' => $this->translate('Yesterday'),
        'last7Days' => $this->translate('Last 7 days'),
        'last14Days' => $this->translate('Last 14 days'),
        'thisMonth' => $this->translate('This month'),
        'lastMonth' => $this->translate('Last month'),
        'beforeLastMonth' => $this->translate('Month before last'),
        'thisYear' => $this->translate('This year'),
        'lastYear' => $this->translate('Last year'),
      ],
      'default' => 0,
    ]);

    $fUserOptions = [];
    foreach ($this->getModel(User::class)->record->where('is_active', true)->get() as $value) {
      $fUserOptions[$value->id] = $value->nick;
    }
    $description->addFilter('fWorker', [
      'title' => $this->translate('Worker'),
      'type' => 'multipleSelectButtons',
      'options' => $fUserOptions,
    ]);
    
    return $description;
  }

}
