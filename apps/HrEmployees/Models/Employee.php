<?php

namespace Hubleto\App\Community\HrEmployees\Models;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Settings\Models\Team;
use Hubleto\App\Community\Workflow\Models\Workflow as WorkflowModel;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;

class Employee extends \Hubleto\Erp\Model
{
  public string $table = 'hr_employees';
  public string $recordManagerClass = RecordManagers\Employee::class;

  public array $relations = [
    'USER' => [self::BELONGS_TO, User::class, 'id_user', 'id'],
    'TEAM' => [self::BELONGS_TO, Team::class, 'id_team', 'id'],
    'MANAGER' => [self::BELONGS_TO, User::class, 'id_manager', 'id'],
    'WORKFLOW' => [self::HAS_ONE, WorkflowModel::class, 'id', 'id_workflow'],
    'WORKFLOW_STEP' => [self::HAS_ONE, WorkflowStep::class, 'id', 'id_workflow_step'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_user' => (new Lookup($this, $this->translate('User account'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setRequired(),
      'employee_number' => (new Varchar($this, $this->translate('Employee number')))->setDefaultVisible()->setRequired(),
      'job_title' => (new Varchar($this, $this->translate('Job title')))->setDefaultVisible(),
      'id_team' => (new Lookup($this, $this->translate('Team'), Team::class))->setDefaultVisible(),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class))->setReactComponent('InputUserSelect'),
      'employment_type' => (new Varchar($this, $this->translate('Employment type')))->setDefaultVisible()->setRequired(),
      'employment_status' => (new Varchar($this, $this->translate('Employment status')))->setDefaultVisible()->setRequired(),
      'date_hired' => (new Date($this, $this->translate('Hire date')))->setDefaultVisible()->setRequired(),
      'date_ended' => (new Date($this, $this->translate('End date'))),
      'work_location' => (new Varchar($this, $this->translate('Work location'))),
      'notes' => (new Text($this, $this->translate('Employment notes'))),
      'id_workflow' => (new Lookup($this, $this->translate('Workflow'), WorkflowModel::class))->setReadonly(),
      'id_workflow_step' => (new Lookup($this, $this->translate('Lifecycle step'), WorkflowStep::class))->setDefaultVisible()->setReadonly(),
    ]);
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();
    $description->inputs['employment_type']->setPredefinedValues([
      $this->translate('Full-time'),
      $this->translate('Part-time'),
      $this->translate('Contract'),
      $this->translate('Temporary'),
      $this->translate('Internship'),
    ]);
    $description->inputs['employment_status']->setPredefinedValues([
      $this->translate('Active'),
      $this->translate('On leave'),
      $this->translate('Ended'),
    ]);
    return $description;
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = $this->translate('Add employee profile');
    $description->addFilter('fEmployeeWorkflowStep', WorkflowModel::buildTableFilterForWorkflowSteps($this, $this->translate('Lifecycle step')));
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }

  public function getRelationsIncludedInLoadTableData(): array|null
  {
    return ['USER', 'TEAM', 'MANAGER', 'WORKFLOW', 'WORKFLOW_STEP'];
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
    $savedRecord = $mWorkflow->applyDefaultWorkflow($savedRecord, 'hr_employees');
    $this->record->recordUpdate($savedRecord);
    return $savedRecord;
  }
}