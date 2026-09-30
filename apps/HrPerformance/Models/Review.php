<?php

namespace Hubleto\App\Community\HrPerformance\Models;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Workflow\Models\Workflow as WorkflowModel;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;

class Review extends \Hubleto\Erp\Model
{
  public string $table = 'hr_reviews';
  public string $recordManagerClass = RecordManagers\Review::class;
  public array $relations = [
    'EMPLOYEE' => [self::BELONGS_TO, User::class, 'id_user', 'id'],
    'REVIEWER' => [self::BELONGS_TO, User::class, 'id_reviewer', 'id'],
    'WORKFLOW' => [self::HAS_ONE, WorkflowModel::class, 'id', 'id_workflow'],
    'WORKFLOW_STEP' => [self::HAS_ONE, WorkflowStep::class, 'id', 'id_workflow_step'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_user' => (new Lookup($this, $this->translate('Employee'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setRequired(),
      'id_reviewer' => (new Lookup($this, $this->translate('Reviewer'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setRequired(),
      'period' => (new Varchar($this, $this->translate('Review period')))->setDefaultVisible()->setRequired(),
      'date_reviewed' => (new Date($this, $this->translate('Review date')))->setDefaultVisible(),
      'score' => (new Decimal($this, $this->translate('Overall score')))->setDecimals(2),
      'status' => (new Varchar($this, $this->translate('Status')))->setDefaultVisible()->setRequired(),
      'summary' => (new Text($this, $this->translate('Summary and feedback'))),
      'id_workflow' => (new Lookup($this, $this->translate('Workflow'), WorkflowModel::class))->setReadonly(),
      'id_workflow_step' => (new Lookup($this, $this->translate('Review step'), WorkflowStep::class))->setDefaultVisible()->setReadonly(),
    ]);
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();
    $description->inputs['status']->setPredefinedValues([
      $this->translate('Draft'),
      $this->translate('Scheduled'),
      $this->translate('Completed'),
    ]);
    return $description;
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = $this->translate('Schedule review');
    $description->ui['orderBy'] = 'date_reviewed desc';
    $description->addFilter('fReviewWorkflowStep', WorkflowModel::buildTableFilterForWorkflowSteps($this, $this->translate('Review step')));
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }

  public function getRelationsIncludedInLoadTableData(): array|null
  {
    return ['EMPLOYEE', 'REVIEWER', 'WORKFLOW', 'WORKFLOW_STEP'];
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
    $savedRecord = $mWorkflow->applyDefaultWorkflow($savedRecord, 'hr_performance');
    $this->record->recordUpdate($savedRecord);
    return $savedRecord;
  }
}