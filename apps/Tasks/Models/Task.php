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
use Hubleto\App\Community\Settings\Models\User;
use Hubleto\App\Community\Pipeline\Models\Pipeline;
use Hubleto\App\Community\Pipeline\Models\PipelineStep;
use Hubleto\App\Community\Contacts\Models\Contact;
use Hubleto\App\Community\Customers\Models\Customer;

class Task extends \Hubleto\Erp\Model
{
  public string $table = 'tasks';
  public string $recordManagerClass = RecordManagers\Task::class;
  public ?string $lookupSqlValue = 'concat(ifnull({%TABLE%}.identifier, ""), " ", ifnull({%TABLE%}.title, ""))';
  public ?string $lookupUrlDetail = 'tasks/{%ID%}';

  public array $relations = [
    'DEVELOPER' => [ self::BELONGS_TO, User::class, 'id_developer', 'id' ],
    'TESTER' => [ self::BELONGS_TO, User::class, 'id_tester', 'id' ],
    'CUSTOMER' => [ self::HAS_ONE, Customer::class, 'id_customer', 'id' ],
    'CONTACT' => [ self::HAS_ONE, Contact::class, 'id_contact', 'id' ],
    // 'DEALS' => [ self::HAS_MANY, DealTask::class, 'id_task', 'id' ],
    // 'PROJECTS' => [ self::HAS_MANY, ProjectTask::class, 'id_task', 'id' ],
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
      'title' => (new Varchar($this, $this->translate('Title')))->setProperty('defaultVisibility', true)->setRequired()->setCssClass('font-bold'),
      'description' => (new Text($this, $this->translate('Description'))),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class)),
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class))->setProperty('defaultVisibility', false),
      'id_developer' => (new Lookup($this, $this->translate('Developer'), User::class))->setReactComponent('InputUserSelect')->setProperty('defaultVisibility', true)->setRequired()
        ->setDefaultValue($this->getAuthProvider()->getUserId())
      ,
      'id_tester' => (new Lookup($this, $this->translate('Tester'), User::class))->setReactComponent('InputUserSelect')->setProperty('defaultVisibility', true)->setRequired()
        ->setDefaultValue($this->getAuthProvider()->getUserId())
      ,
      'priority' => (new Integer($this, $this->translate('Priority'))),
      'hours_estimation' => (new Decimal($this, $this->translate('Estimation')))->setProperty('defaultVisibility', true)->setUnit('h')->setDecimals(2),
      'duration_days' => (new Integer($this, $this->translate('Duration')))->setProperty('defaultVisibility', true)->setUnit('days'),
      'date_start' => (new Date($this, $this->translate('Start')))->setDefaultValue(date("Y-m-d")),
      'date_deadline' => (new Date($this, $this->translate('Deadline')))->setDefaultValue(date("Y-m-d")),
      'id_pipeline' => (new Lookup($this, $this->translate('Pipeline'), Pipeline::class)),
      'id_pipeline_step' => (new Lookup($this, $this->translate('Pipeline step'), PipelineStep::class))->setProperty('defaultVisibility', true),
      'is_chargeable' => (new Boolean($this, $this->translate('Is chargeable')))->setDefaultValue(true),
      'is_milestone' => (new Boolean($this, $this->translate('Is milestone')))->setDefaultValue(false),
      'is_closed' => (new Boolean($this, $this->translate('Closed')))->setDefaultValue(false),
      // 'id_project' => (new Lookup($this, $this->translate('Project'), Project::class))->setProperty('defaultVisibility', true),
      'shared_folder' => (new Varchar($this, "Shared folder (online document storage)"))->setReactComponent('InputHyperlink'),
      'notes' => (new Text($this, $this->translate('Notes'))),
      'date_created' => (new DateTime($this, $this->translate('Created')))->setReadonly()->setDefaultValue(date("Y-m-d H:i:s")),
      'virt_worked' => (new Virtual($this, $this->translate('Worked')))->setProperty('defaultVisibility', true)->setUnit("hours")
        ->setProperty('sql', "select sum(ifnull(worked_hours, 0)) from worksheet_activities where id_task = tasks.id")
      ,
      'virt_related_to' => (new Virtual($this, $this->translate('Related to')))->setProperty('defaultVisibility', true)
        ->setProperty('sql', "
          select
            concat(
              ifnull(group_concat(concat('D:', deals.identifier) separator ', '), ''),
              ifnull(group_concat(concat('P:', projects.identifier) separator ', '), '')
            )
          from tasks t2
          left join deals_tasks on deals_tasks.id_task = t2.id
          left join projects_tasks on projects_tasks.id_task = t2.id
          left join deals on deals.id = deals_tasks.id_deal
          left join projects on projects.id = projects_tasks.id_project
          where
            t2.id = tasks.id 
            and (
              deals_tasks.id_task = tasks.id
              or projects_tasks.id_task = tasks.id
            )
        ")
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

    $description->ui['filters'] = [
      'fTaskPipelineStep' => Pipeline::buildTableFilterForPipelineSteps($this, 'Status'),
      'fTaskClosed' => [
        'title' => $this->translate('Open / Closed'),
        'options' => [
          0 => $this->translate('Open'),
          1 => $this->translate('Closed'),
          2 => $this->translate('All'),
        ],
        'default' => 0,
      ],
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
