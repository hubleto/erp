<?php

namespace Hubleto\App\Community\Workflow\Models;

use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Integer;

class Workflow extends \Hubleto\Erp\Model
{
  public string $table = 'workflows';
  public string $recordManagerClass = RecordManagers\Workflow::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public array $relations = [
    'STEPS' => [ self::HAS_MANY, WorkflowStep::class, 'id_workflow', 'id' ]
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired()->setDefaultVisible()->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      'order' => (new Integer($this, $this->translate('Order')))->setRequired()->setDefaultVisible(),
      'description' => (new Varchar($this, $this->translate('Description')))->setDefaultVisible(),
      'show_in_kanban' => (new Boolean($this, $this->translate('Show in kanban')))->setDefaultVisible(),
      'group' => (new Varchar($this, $this->translate('Group')))->setRequired()->setDefaultVisible()->setPredefinedValues([
        'deals',
        'orders',
        'projects',
        'tasks',
        'leads',
        'campaigns',
      ])->addIndex('INDEX `group` (`group`)'),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    // $description->ui['title'] = 'Workflows';
    $description->ui['addButtonText'] = 'Add Workflow';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

  public function getDefaultWorkflowInGroup(string $group): array
  {
    $defaultWorkflow = $this->record->where('group', $group)->with('STEPS')->first()?->toArray();

    $idWorkflow = 0;
    $idWorkflowStep = 0;
    if (is_array($defaultWorkflow)) {
      $idWorkflow = $defaultWorkflow['id'] ?? 0;
      if (is_array($defaultWorkflow['STEPS'])) {
        $idWorkflowStep = reset($defaultWorkflow['STEPS'])['id'] ?? 0;
      }
    }

    return [$defaultWorkflow, $idWorkflow, $idWorkflowStep];
  }

  public static function buildTableFilterForWorkflowSteps(\Hubleto\Erp\Model $model, string $title): array
  {
    $fWorkflowSteps = [
      'title' => $title,
      'type' => 'multipleSelectButtons',
      'options' => [],
      'colors' => [],
    ];
    $workflowStepsUsed = $model->record->with('WORKFLOW_STEP')->groupBy('id_workflow_step')->get();
    if ($workflowStepsUsed) {
      foreach ($workflowStepsUsed as $step) {
        if ($step->WORKFLOW_STEP) {
          $fWorkflowSteps['options'][$step->WORKFLOW_STEP->id] = $step->WORKFLOW_STEP->name;
          $fWorkflowSteps['colors'][$step->WORKFLOW_STEP->id] = $step->WORKFLOW_STEP->color;
        }
      }
    }
    return $fWorkflowSteps;
  }

}
