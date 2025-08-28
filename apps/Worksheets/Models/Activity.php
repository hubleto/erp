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
use Hubleto\App\Community\Settings\Models\User;
use Hubleto\App\Community\Tasks\Models\Task;

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
      'id_worker' => (new Lookup($this, $this->translate('Worker'), User::class))->setProperty('defaultVisibility', true)->setDefaultValue($this->getAuthProvider()->getUserId())->setRequired(),
      'id_task' => (new Lookup($this, $this->translate('Task'), Task::class))->setProperty('defaultVisibility', true)->setRequired(),
      'id_type' => (new Lookup($this, $this->translate('Type'), ActivityType::class))->setProperty('defaultVisibility', true),
      'date_worked' => (new Date($this, $this->translate('Day')))->setProperty('defaultVisibility', true)->setDefaultValue(date("Y-m-d")),
      'duration' => (new Decimal($this, $this->translate('Duration')))->setProperty('defaultVisibility', true)->setDecimals(2)->setUnit('hours'),
      'description' => (new Text($this, $this->translate('Description')))->setProperty('defaultVisibility', true),
      'is_approved' => (new Boolean($this, $this->translate('Approved')))->setProperty('defaultVisibility', true),
      'datetime_created' => (new DateTime($this, $this->translate('Created')))->setDefaultValue(date("Y-m-d H:i:s")),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add Activity';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

}
