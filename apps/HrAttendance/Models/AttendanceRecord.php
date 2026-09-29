<?php

namespace Hubleto\App\Community\HrAttendance\Models;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Time;
use Hubleto\Framework\Db\Column\Varchar;

class AttendanceRecord extends \Hubleto\Erp\Model
{
  public string $table = 'hr_attendance_records';
  public string $recordManagerClass = RecordManagers\AttendanceRecord::class;
  public array $relations = [
    'USER' => [self::BELONGS_TO, User::class, 'id_user', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_user' => (new Lookup($this, $this->translate('Employee'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setRequired(),
      'date_worked' => (new Date($this, $this->translate('Work date')))->setDefaultVisible()->setRequired(),
      'time_in' => (new Time($this, $this->translate('Clock in'))),
      'time_out' => (new Time($this, $this->translate('Clock out'))),
      'break_minutes' => (new Decimal($this, $this->translate('Break (minutes)')))->setDecimals(0)->setDefaultValue(0),
      'status' => (new Varchar($this, $this->translate('Status')))->setDefaultVisible()->setRequired(),
      'is_approved' => (new Boolean($this, $this->translate('Approved')))->setDefaultVisible(),
      'notes' => (new Text($this, $this->translate('Notes'))),
    ]);
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();
    $description->inputs['status']->setPredefinedValues([
      $this->translate('Present'),
      $this->translate('Late'),
      $this->translate('Absent'),
      $this->translate('Remote'),
      $this->translate('On leave'),
    ]);
    return $description;
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = $this->translate('Attendance records');
    $description->ui['addButtonText'] = $this->translate('Add attendance record');
    $description->ui['orderBy'] = 'date_worked desc';
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }

  public function getRelationsIncludedInLoadTableData(): array|null
  {
    return ['USER'];
  }

  public function getMaxReadLevelForLoadTableData(): int
  {
    return 1;
  }
}