<?php

namespace Hubleto\App\Community\HrLeave\Models;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Workflow\Models\Workflow as WorkflowModel;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;

class LeaveRequest extends \Hubleto\Erp\Model
{
  public string $table = 'hr_leave_requests';
  public string $recordManagerClass = RecordManagers\LeaveRequest::class;
  public array $relations = [
    'USER' => [self::BELONGS_TO, User::class, 'id_user', 'id'],
    'LEAVE_TYPE' => [self::BELONGS_TO, LeaveType::class, 'id_leave_type', 'id'],
    'APPROVER' => [self::BELONGS_TO, User::class, 'id_approver', 'id'],
    'WORKFLOW' => [self::HAS_ONE, WorkflowModel::class, 'id', 'id_workflow'],
    'WORKFLOW_STEP' => [self::HAS_ONE, WorkflowStep::class, 'id', 'id_workflow_step'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_user' => (new Lookup($this, $this->translate('Employee'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setRequired(),
      'id_leave_type' => (new Lookup($this, $this->translate('Leave type'), LeaveType::class))->setDefaultVisible()->setRequired(),
      'date_from' => (new Date($this, $this->translate('From')))->setDefaultVisible()->setRequired(),
      'date_to' => (new Date($this, $this->translate('To')))->setDefaultVisible()->setRequired(),
      'balance_year' => (new Integer($this, $this->translate('Balance year')))->setDefaultVisible()->setRequired()->setDefaultValue((int) date('Y')),
      'days_requested' => (new Decimal($this, $this->translate('Days requested')))->setDecimals(2)->setRequired(),
      'status' => (new Varchar($this, $this->translate('Status')))->setDefaultVisible()->setRequired(),
      'id_approver' => (new Lookup($this, $this->translate('Approver'), User::class))->setReactComponent('InputUserSelect'),
      'date_decided' => (new Date($this, $this->translate('Decision date'))),
      'reason' => (new Text($this, $this->translate('Reason'))),
      'id_workflow' => (new Lookup($this, $this->translate('Workflow'), WorkflowModel::class))->setReadonly(),
      'id_workflow_step' => (new Lookup($this, $this->translate('Approval step'), WorkflowStep::class))->setDefaultVisible()->setReadonly(),
    ]);
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();
    $description->inputs['status']->setPredefinedValues([
      $this->translate('Pending'),
      $this->translate('Approved'),
      $this->translate('Rejected'),
      $this->translate('Cancelled'),
    ]);
    return $description;
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = $this->translate('Request leave');
    $description->ui['orderBy'] = 'date_from desc';
    $description->addFilter('fLeaveWorkflowStep', WorkflowModel::buildTableFilterForWorkflowSteps($this, $this->translate('Approval step')));
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }

  public function getRelationsIncludedInLoadTableData(): array|null
  {
    return ['USER', 'LEAVE_TYPE', 'APPROVER', 'WORKFLOW', 'WORKFLOW_STEP'];
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
    $savedRecord = $mWorkflow->applyDefaultWorkflow($savedRecord, 'hr_leave');
    $this->record->recordUpdate($savedRecord);
    return $savedRecord;
  }
}