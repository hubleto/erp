<?php

namespace Hubleto\App\Community\Tasks\Models;

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
use Hubleto\App\Community\Projects\Models\Project;
use Hubleto\App\Community\Settings\Models\User;
use Hubleto\App\Community\Pipeline\Models\Pipeline;
use Hubleto\App\Community\Pipeline\Models\PipelineStep;

class Task extends \Hubleto\Erp\Model
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

  /**
   * [Description for describeColumns]
   *
   * @return array
   * 
   */
  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'identifier' => (new Varchar($this, $this->translate('Identifier')))->setProperty('defaultVisibility', true)->setCssClass('badge badge-info')->setDescription('Leave empty to generate automatically.'),
      'title' => (new Varchar($this, $this->translate('Title')))->setProperty('defaultVisibility', true)->setRequired(),
      'description' => (new Text($this, $this->translate('Description'))),
      'id_developer' => (new Lookup($this, $this->translate('Developer'), User::class))->setProperty('defaultVisibility', true)->setRequired()
        ->setDefaultValue($this->getAuthProvider()->getUserId())
      ,
      'id_tester' => (new Lookup($this, $this->translate('Tester'), User::class))->setProperty('defaultVisibility', true)->setRequired()
        ->setDefaultValue($this->getAuthProvider()->getUserId())
      ,
      'priority' => (new Integer($this, $this->translate('Priority'))),
      'hours_estimation' => (new Decimal($this, $this->translate('Estimation')))->setProperty('defaultVisibility', true)->setUnit('h')->setDecimals(2),
      'duration_days' => (new Integer($this, $this->translate('Duration')))->setProperty('defaultVisibility', true)->setUnit('days'),
      'date_start' => (new Date($this, $this->translate('Start')))->setDefaultValue(date("Y-m-d")),
      'date_deadline' => (new Date($this, $this->translate('Deadline')))->setDefaultValue(date("Y-m-d")),
      'id_pipeline' => (new Lookup($this, $this->translate('Pipeline'), Pipeline::class))->setDefaultValue(1),
      'id_pipeline_step' => (new Lookup($this, $this->translate('Pipeline step'), PipelineStep::class))->setDefaultValue(null)->setProperty('defaultVisibility', true),
      'is_milestone' => (new Boolean($this, $this->translate('Is milestone')))->setDefaultValue(false),
      'is_closed' => (new Boolean($this, $this->translate('Closed')))->setDefaultValue(false),
      // 'id_project' => (new Lookup($this, $this->translate('Project'), Project::class))->setProperty('defaultVisibility', true),
      'shared_folder' => (new Varchar($this, "Shared folder (online document storage)"))->setReactComponent('InputHyperlink'),
      'notes' => (new Text($this, $this->translate('Notes'))),
      'date_created' => (new DateTime($this, $this->translate('Created')))->setReadonly()->setDefaultValue(date("Y-m-d H:i:s")),
      'virt_worked' => (new Virtual($this, $this->translate('Worked')))->setProperty('defaultVisibility', true)->setUnit("hours")
        ->setProperty('sql', "select sum(ifnull(duration, 0)) from worksheet_activities where id_task = tasks.id")
      ,

    ]);
  }

  /**
   * [Description for describeTable]
   *
   * @return \Hubleto\Framework\Description\Table
   * 
   */
  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add Task';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;
    $description->ui['showFooter'] = false;

    $description->ui['defaultFilters'] = [
      'fTaskPipelineStep' => Pipeline::buildTableDefaultFilterForPipelineSteps($this, 'Status'),
    ];

    return $description;
  }

  /**
   * [Description for onAfterCreate]
   *
   * @param array $savedRecord
   * 
   * @return array
   * 
   */
  public function onAfterCreate(array $savedRecord): array
  {

    $mPipeline = $this->getService(Pipeline::class);
    list($defaultPipeline, $idPipeline, $idPipelineStep) = $mPipeline->getDefaultPipelineInGroup('tasks');
    $savedRecord['id_pipeline'] = $idPipeline;
    $savedRecord['id_pipeline_step'] = $idPipelineStep;

    if (empty($savedRecord['identifier'])) {
      $savedRecord["identifier"] = '#' . $savedRecord["id"];
    }

    $this->record->recordUpdate($savedRecord);

    return parent::onAfterCreate($savedRecord);
  }

}
