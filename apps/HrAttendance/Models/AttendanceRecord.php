<?php

namespace Hubleto\App\Community\HrAttendance\Models;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Workflow\Models\Workflow as WorkflowModel;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;
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
    'WORKFLOW' => [self::HAS_ONE, WorkflowModel::class, 'id', 'id_workflow'],
    'WORKFLOW_STEP' => [self::HAS_ONE, WorkflowStep::class, 'id', 'id_workflow_step'],
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
      'id_workflow' => (new Lookup($this, $this->translate('Workflow'), WorkflowModel::class))->setReadonly(),
      'id_workflow_step' => (new Lookup($this, $this->translate('Approval step'), WorkflowStep::class))->setDefaultVisible()->setReadonly(),
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
    $description->ui['addButtonText'] = $this->translate('Add attendance record');
    $description->ui['orderBy'] = 'date_worked desc';
    $description->addFilter('fAttendanceWorkflowStep', WorkflowModel::buildTableFilterForWorkflowSteps($this, $this->translate('Approval step')));
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }

  public function getRelationsIncludedInLoadTableData(): array|null
  {
    return ['USER', 'WORKFLOW', 'WORKFLOW_STEP'];
  }

  public function getMaxReadLevelForLoadTableData(): int
  {
    return 1;
  }

  public function onAfterCreate(array $savedRecord): array
  {
    $savedRecord = parent::onAfterCreate($savedRecord);
    /** @var WorkflowModel */
    $mWorkflow = $this->getModel(WorkflowModel::class);
    $savedRecord = $mWorkflow->applyDefaultWorkflow($savedRecord, 'hr_attendance');
    $this->record->recordUpdate($savedRecord);
    return $savedRecord;
  }
}