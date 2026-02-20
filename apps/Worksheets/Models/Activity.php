<?php

namespace Hubleto\App\Community\Worksheets\Models;


use Hubleto\App\Community\Worksheets\Loader as WorksheetsApp;
use Hubleto\Framework\Exceptions\RecordSaveException;

use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Virtual;
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
  public ?string $lookupUrlAdd = 'worksheets/add';
  public ?string $lookupUrlDetail = 'worksheets/{%ID%}';

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
      'virt_month' => (new Virtual($this, $this->translate('Month')))->setDefaultVisible()
        ->setProperty('sql', "concat(YEAR(`date_worked`), '-', LPAD(MONTH(`date_worked`), 2, '0'))"),
      'virt_customer' => (new Virtual($this, $this->translate('Customer')))->setDefaultVisible()
        ->setProperty('sql', "
          SELECT `c`.`identifier` FROM `customers` `c`
          LEFT JOIN `cusstomers` `c` ON `c`.`id` = `t`.`id_customer`
        "),
      'virt_deal' => (new Virtual($this, $this->translate('Deal')))->setDefaultVisible()
        ->setProperty('sql', "
          SELECT `d`.`identifier` FROM `deals_tasks` `dt`
          LEFT JOIN `deals` `d` ON `d`.`id` = `dt`.`id_deal`
          WHERE `dt`.`id_task` = `worksheet_activities`.`id_task`
        "),
      'virt_project' => (new Virtual($this, $this->translate('Project')))->setDefaultVisible()
        ->setProperty('sql', "
          SELECT `p`.`identifier` FROM `projects_tasks` `pt`
          LEFT JOIN `projects` `p` ON `p`.`id` = `pt`.`id_project`
          WHERE `pt`.`id_task` = `worksheet_activities`.`id_task`
        "),
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

    $filters = $this->router()->urlParamAsArray("filters");

    if (isset($filters['fGroupBy'])) {
      $fGroupBy = (array) $filters['fGroupBy'];

      $showOnlyColumns = [];
      if (in_array('task', $fGroupBy)) $showOnlyColumns[] = 'id_task';
      if (in_array('customer', $fGroupBy)) $showOnlyColumns[] = 'virt_customer';
      if (in_array('type', $fGroupBy)) $showOnlyColumns[] = 'id_type';
      if (in_array('project', $fGroupBy)) $showOnlyColumns[] = 'virt_project';
      if (in_array('deal', $fGroupBy)) $showOnlyColumns[] = 'virt_deal';
      if (in_array('worker', $fGroupBy)) $showOnlyColumns[] = 'id_worker';
      if (in_array('month', $fGroupBy)) $showOnlyColumns[] = 'virt_month';

      $description->showOnlyColumns($showOnlyColumns);

      $description->addColumn(
        'total_worked_hours',
        (new Decimal($this, $this->translate('Total worked hours')))->setUnit('hours')->setDecimals(2)->setCssClass('badge badge-warning')
      );
      // $description->hideColumns(['worked_hours']);

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

    $description->addFilter('fGroupBy', [
      'title' => $this->translate('Group by'),
      'type' => 'multipleSelectButtons',
      'options' => [
        'task' => $this->translate('Task'),
        'customer' => $this->translate('Customer'),
        'type' => $this->translate('Type'),
        'project' => $this->translate('Project'),
        'deal' => $this->translate('Deal'),
        'worker' => $this->translate('Worker'),
        'month' => $this->translate('Month'),
      ]
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

  public function validateDateDiff(array $record): void
  {
    $days = date_diff(new \DateTimeImmutable($record['date_worked']), new \DateTimeImmutable())->days;
    $daysDiffMax = $this->config()->forApp(WorksheetsApp::class)->getAsInteger('activityDaysDiffMax', 5);

    $userType = $this->authProvider()->getUserType();

    if ($userType != User::TYPE_ADMINISTRATOR && $days > $daysDiffMax) {
      throw new RecordSaveException('You may not create or modify activity more than ' . $daysDiffMax . ' days after it has been performed.');
    }
  }

  public function onBeforeCreate(array $record): array
  {
    $this->validateDateDiff($record);
    return parent::onBeforeCreate($record);
  }

  public function onBeforeUpdate(array $record): array
  {
    $this->validateDateDiff($record);
    return parent::onBeforeUpdate($record);
  }

}
