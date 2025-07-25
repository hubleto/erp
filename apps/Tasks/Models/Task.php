<?php

namespace HubletoApp\Community\Tasks\Models;

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
use Hubleto\Framework\Db\Column\Virtual;
use HubletoApp\Community\Projects\Models\Project;
use HubletoApp\Community\Settings\Models\User;
use HubletoApp\Community\Pipeline\Models\Pipeline;
use HubletoApp\Community\Pipeline\Models\PipelineStep;

class Task extends \Hubleto\Framework\Models\Model
{
  public string $table = 'tasks';
  public string $recordManagerClass = RecordManagers\Task::class;
  public ?string $lookupSqlValue = 'concat(ifnull({%TABLE%}.identifier, ""), " ", ifnull({%TABLE%}.title, ""))';
  public ?string $lookupUrlDetail = 'tasks/{%ID%}';

  public array $relations = [
    'PROJECT' => [ self::BELONGS_TO, Project::class, 'id_project', 'id' ],
    'DEVELOPER' => [ self::BELONGS_TO, User::class, 'id_developer', 'id' ],
    'TESTER' => [ self::BELONGS_TO, User::class, 'id_tester', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'identifier' => (new Varchar($this, $this->translate('Identifier')))->setProperty('defaultVisibility', true)->setCssClass('badge badge-warning text-xl')->setDescription('Leave empty to generate automatically.'),
      'title' => (new Varchar($this, $this->translate('Title')))->setProperty('defaultVisibility', true)->setRequired(),
      'description' => (new Text($this, $this->translate('Description'))),
      'id_developer' => (new Lookup($this, $this->translate('Developer'), User::class))->setProperty('defaultVisibility', true)->setRequired()
        ->setDefaultValue($this->main->auth->getUserId())
      ,
      'id_tester' => (new Lookup($this, $this->translate('Tester'), User::class))->setProperty('defaultVisibility', true)->setRequired()
        ->setDefaultValue($this->main->auth->getUserId())
      ,
      'priority' => (new Integer($this, $this->translate('Priority'))),
      'hours_estimation' => (new Decimal($this, $this->translate('Estimation')))->setProperty('defaultVisibility', true)->setUnit('hours'),
      'duration_days' => (new Integer($this, $this->translate('Duration')))->setProperty('defaultVisibility', true)->setUnit('days'),
      'date_start' => (new Date($this, $this->translate('Start')))->setReadonly()->setDefaultValue(date("Y-m-d")),
      'date_deadline' => (new Date($this, $this->translate('Deadline')))->setReadonly()->setDefaultValue(date("Y-m-d")),
      'id_pipeline' => (new Lookup($this, $this->translate('Pipeline'), Pipeline::class))->setDefaultValue(1),
      'id_pipeline_step' => (new Lookup($this, $this->translate('Pipeline step'), PipelineStep::class))->setDefaultValue(null),
      'is_milestone' => (new Boolean($this, $this->translate('Is milestone')))->setDefaultValue(false),
      'is_closed' => (new Boolean($this, $this->translate('Closed')))->setDefaultValue(false),
      // 'id_project' => (new Lookup($this, $this->translate('Project'), Project::class))->setProperty('defaultVisibility', true),
      'shared_folder' => (new Varchar($this, "Shared folder (online document storage)"))->setReactComponent('InputHyperlink'),
      'notes' => (new Text($this, $this->translate('Notes'))),
      'date_created' => (new DateTime($this, $this->translate('Created')))->setReadonly()->setDefaultValue(date("Y-m-d H:i:s")),

      'external_model' => (new Varchar($this, $this->translate('External Model')))->setProperty('defaultVisibility', true),
      'external_id' => (new Integer($this, $this->translate('External ID'))),

      'virt_worked' => (new Virtual($this, $this->translate('Worked')))->setProperty('defaultVisibility', true)->setUnit("hours")
        ->setProperty('sql', "select sum(ifnull(duration, 0)) from worksheet_activities where id_task = tasks.id")
      ,

    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add Task';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;
    $description->ui['showFooter'] = false;

    $tasksApp = $this->main->apps->community('Tasks');

    if (isset($description->columns['external_model'])) {
      $enumExternalModels = ['' => '-- No external relation --'];
      foreach ($tasksApp->getRegisteredExternalModels() as $modelClass => $app) {
        $enumExternalModels[$modelClass] = $app->manifest['nameTranslated'];
      }

      $description->columns['external_model']->setEnumValues($enumExternalModels);
    }

    $fExternalModels = [];
    foreach ($tasksApp->getRegisteredExternalModels() as $modelClass => $app) {
      $fExternalModels[$modelClass] = $app->manifest['name'];
    }
    $description->ui['defaultFilters'] = [
      'fExternalModels' => [ 'title' => 'External models', 'type' => 'multipleSelectButtons', 'options' => $fExternalModels ],
    ];

    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();

    $tasksApp = $this->main->apps->community('Tasks');

    $enumExternalModels = ['' => '-- No external relation --'];
    foreach ($tasksApp->getRegisteredExternalModels() as $modelClass => $app) {
      $enumExternalModels[$modelClass] = $app->manifest['nameTranslated'];
    }

    $description->inputs['external_model']->setEnumValues($enumExternalModels);

    return $description;
  }

  public function onBeforeCreate(array $record): array
  {
    return parent::onBeforeCreate($record);
  }

  public function onBeforeUpdate(array $record): array
  {
    return parent::onBeforeUpdate($record);
  }

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    return parent::onAfterUpdate($originalRecord, $savedRecord);
  }

  public function onAfterCreate(array $savedRecord): array
  {

    $mPipeline = $this->main->di->create(Pipeline::class);
    list($defaultPipeline, $idPipeline, $idPipelineStep) = $mPipeline->getDefaultPipelineInfo(Pipeline::TYPE_TASK_MANAGEMENT);
    $savedRecord['id_pipeline'] = $idPipeline;
    $savedRecord['id_pipeline_step'] = $idPipelineStep;

    if (empty($savedRecord['identifier'])) {
      // \$mProject = $this->main->di->create(\HubletoApp\Community\Projects\Models\Project::class);
      // $project = $mProject->record->where("id", $savedRecord["id_project"])->first()?->toArray();
      // $savedRecord["identifier"] = ($project["identifier"] ?? 'T') . '#' . $savedRecord["id"];
      $tasksApp = $this->main->apps->community('Tasks');
      $externalModelApp = $tasksApp->getRegisteredExternalModels()[$savedRecord['external_model']] ?? null;
      if ($externalModelApp) {
        $externalModelClass = $savedRecord['external_model'];
        $externalModel = $this->main->di->create($externalModelClass);
        $externalRecord = $externalModel->record->prepareReadQuery()->where($externalModel->table.'.id', $savedRecord['external_id'])->first()?->toArray();
        $savedRecord["identifier"] =
          ($externalModelApp->manifest['name'] ?? 'X')
          . ':' . ($externalRecord['identifier'] ?? 'X')
          . '#' . $savedRecord["id"]
        ;
      }
    }

    $this->record->recordUpdate($savedRecord);

    return parent::onAfterCreate($savedRecord);
  }

}
