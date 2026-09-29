<?php

namespace Hubleto\App\Community\HrRecruitment\Models;

use Hubleto\App\Community\Workflow\Models\Workflow as WorkflowModel;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;

class Application extends \Hubleto\Erp\Model
{
  public string $table = 'hr_applications';
  public string $recordManagerClass = RecordManagers\Application::class;
  public ?string $lookupSqlValue = 'concat("Application #", {%TABLE%}.id)';
  public array $relations = [
    'JOB_OPENING' => [self::BELONGS_TO, JobOpening::class, 'id_job_opening', 'id'],
    'CANDIDATE' => [self::BELONGS_TO, Candidate::class, 'id_candidate', 'id'],
    'WORKFLOW' => [self::HAS_ONE, WorkflowModel::class, 'id', 'id_workflow'],
    'WORKFLOW_STEP' => [self::HAS_ONE, WorkflowStep::class, 'id', 'id_workflow_step'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_job_opening' => (new Lookup($this, $this->translate('Job opening'), JobOpening::class))->setDefaultVisible()->setRequired(),
      'id_candidate' => (new Lookup($this, $this->translate('Candidate'), Candidate::class))->setDefaultVisible()->setRequired(),
      'date_applied' => (new Date($this, $this->translate('Applied on')))->setDefaultVisible()->setRequired(),
      'date_decided' => (new Date($this, $this->translate('Decision date'))),
      'notes' => (new Text($this, $this->translate('Notes'))),
      'id_workflow' => (new Lookup($this, $this->translate('Workflow'), WorkflowModel::class))->setReadonly(),
      'id_workflow_step' => (new Lookup($this, $this->translate('Pipeline step'), WorkflowStep::class))->setDefaultVisible()->setReadonly(),
    ]);
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    return parent::describeForm();
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = $this->translate('Add application');
    $description->addFilter('fApplicationWorkflowStep', WorkflowModel::buildTableFilterForWorkflowSteps($this, $this->translate('Pipeline step')));
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }

  public function getRelationsIncludedInLoadTableData(): array|null
  {
    return ['JOB_OPENING', 'CANDIDATE', 'WORKFLOW', 'WORKFLOW_STEP'];
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
    $savedRecord = $mWorkflow->applyDefaultWorkflow($savedRecord, 'hr_recruitment');
    $this->record->recordUpdate($savedRecord);
    return $savedRecord;
  }
}