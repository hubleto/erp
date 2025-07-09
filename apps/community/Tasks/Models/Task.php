<?php

namespace HubletoApp\Community\Tasks\Models;

use \ADIOS\Core\Db\Column\Boolean;
use \ADIOS\Core\Db\Column\Color;
use \ADIOS\Core\Db\Column\Decimal;
use \ADIOS\Core\Db\Column\Date;
use \ADIOS\Core\Db\Column\DateTime;
use \ADIOS\Core\Db\Column\File;
use \ADIOS\Core\Db\Column\Image;
use \ADIOS\Core\Db\Column\Integer;
use \ADIOS\Core\Db\Column\Json;
use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Password;
use \ADIOS\Core\Db\Column\Text;
use \ADIOS\Core\Db\Column\Varchar;

use \HubletoApp\Community\Projects\Models\Project;
use \HubletoApp\Community\Settings\Models\User;
use HubletoApp\Community\Pipeline\Models\Pipeline;
use HubletoApp\Community\Pipeline\Models\PipelineStep;

class Task extends \HubletoMain\Core\Models\Model
{

  public string $table = 'tasks';
  public string $recordManagerClass = RecordManagers\Task::class;
  public ?string $lookupSqlValue = 'concat(ifnull({%TABLE%}.identifier, ""), " ", ifnull({%TABLE%}.title, ""))';

  public array $relations = [ 
    'PROJECT' => [ self::BELONGS_TO, Project::class, 'id_project', 'id' ],
    'DEVELOPER' => [ self::BELONGS_TO, User::class, 'id_developer', 'id' ],
    'TESTER' => [ self::BELONGS_TO, User::class, 'id_tester', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'identifier' => (new Varchar($this, $this->translate('Identifier')))->setProperty('defaultVisibility', true)->setRequired()->setCssClass('badge badge-warning text-xl'),
      'title' => (new Varchar($this, $this->translate('Title')))->setProperty('defaultVisibility', true)->setRequired()->setCssClass('text-2xl text-primary'),
      'description' => (new Text($this, $this->translate('Description'))),
      'id_developer' => (new Lookup($this, $this->translate('Developer'), User::class))->setProperty('defaultVisibility', true)->setRequired()
        ->setDefaultValue($this->main->auth->getUserId())
      ,
      'id_tester' => (new Lookup($this, $this->translate('Tester'), User::class))->setProperty('defaultVisibility', true)->setRequired()
        ->setDefaultValue($this->main->auth->getUserId())
      ,
      'manhours_estimation' => (new Decimal($this, $this->translate('Estimation')))->setProperty('defaultVisibility', true)->setUnit('manhours'),
      'id_pipeline' => (new Lookup($this, $this->translate('Pipeline'), Pipeline::class))->setDefaultValue(1),
      'id_pipeline_step' => (new Lookup($this, $this->translate('Pipeline step'), PipelineStep::class))->setDefaultValue(null),
      'is_closed' => (new Boolean($this, $this->translate('Closed')))->setDefaultValue(true),
      'id_project' => (new Lookup($this, $this->translate('Project'), Project::class))->setProperty('defaultVisibility', true),
      'notes' => (new Text($this, $this->translate('Notes'))),
      'date_created' => (new DateTime($this, $this->translate('Created')))->setReadonly()->setDefaultValue(date("Y-m-d H:i:s")),

    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add Task';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    // Uncomment and modify these lines if you want to define defaultFilter for your model
    // $description->ui['defaultFilters'] = [
    //   'fArchive' => [ 'title' => 'Archive', 'options' => [ 0 => 'Active', 1 => 'Archived' ] ],
    // ];

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

    $mPipeline = new Pipeline($this->main);
    list($defaultPipeline, $idPipeline, $idPipelineStep) = $mPipeline->getDefaultPipelineInfo(Pipeline::TYPE_TASK_MANAGEMENT);
    $savedRecord['id_pipeline'] = $idPipeline;
    $savedRecord['id_pipeline_step'] = $idPipelineStep;

    if (empty($savedRecord['identifier'])) {
      $mProject = new \HubletoApp\Community\Projects\Models\Project($this->main);
      $project = $mProject->record->find($savedRecord["id_project"])->first()?->toArray();
      $savedRecord["identifier"] = ($project["identifier"] ?? 'T') . '#' . $savedRecord["id"];
    }

    $this->record->recordUpdate($savedRecord);

    return parent::onAfterCreate($savedRecord);
  }

}
