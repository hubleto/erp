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

use Hubleto\App\Community\Workflow\Models\Workflow;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;
use Hubleto\App\Community\Contacts\Models\Contact;
use Hubleto\App\Community\Customers\Models\Customer;
use Hubleto\App\Community\Auth\Models\User;

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
    'TODO' => [ self::HAS_MANY, Todo::class, 'id_task', 'id' ],
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
      'identifier' => (new Varchar($this, $this->translate('Identifier')))->setDefaultVisible()->setCssClass('badge badge-info')->setDescription('Leave empty to generate automatically.'),
      'title' => (new Varchar($this, $this->translate('Title')))->setDefaultVisible()->setRequired()->setCssClass('font-bold'),
      'description' => (new Text($this, $this->translate('Description'))),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class)),
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class))->setDefaultHidden(),
      'id_developer' => (new Lookup($this, $this->translate('Developer'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setRequired()
        ->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId())
      ,
      'id_tester' => (new Lookup($this, $this->translate('Tester'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setRequired()
        ->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId())
      ,
      'priority' => (new Integer($this, $this->translate('Priority'))),
      'hours_estimation' => (new Decimal($this, $this->translate('Estimation')))->setDefaultVisible()->setUnit('h')->setDecimals(2),
      'duration_days' => (new Integer($this, $this->translate('Duration')))->setDefaultVisible()->setUnit('days'),
      'date_start' => (new Date($this, $this->translate('Start')))->setDefaultValue(date("Y-m-d")),
      'date_deadline' => (new Date($this, $this->translate('Deadline')))->setDefaultValue(date("Y-m-d")),
      'id_workflow' => (new Lookup($this, $this->translate('Workflow'), Workflow::class)),
      'id_workflow_step' => (new Lookup($this, $this->translate('Workflow step'), WorkflowStep::class))->setDefaultVisible(),
      'is_chargeable' => (new Boolean($this, $this->translate('Is chargeable')))->setDefaultValue(true),
      'is_milestone' => (new Boolean($this, $this->translate('Is milestone')))->setDefaultValue(false),
      'is_closed' => (new Boolean($this, $this->translate('Closed')))->setDefaultValue(false),
      // 'id_project' => (new Lookup($this, $this->translate('Project'), Project::class))->setDefaultVisible(),
      'shared_folder' => (new Varchar($this, "Shared folder (online document storage)"))->setReactComponent('InputHyperlink'),
      'notes' => (new Text($this, $this->translate('Notes'))),
      'date_created' => (new DateTime($this, $this->translate('Created')))->setReadonly()->setDefaultValue(date("Y-m-d H:i:s")),
      'virt_worked' => (new Virtual($this, $this->translate('Worked')))->setDefaultVisible()->setUnit("hours")
        ->setProperty('sql', "select sum(ifnull(worked_hours, 0)) from worksheet_activities where id_task = tasks.id")
      ,
      'virt_related_to' => (new Virtual($this, $this->translate('Related to')))->setDefaultVisible()
        ->setProperty('sql', "
          select
            concat(
              group_concat(ifnull(concat(deals.identifier, ' ', deals.title), '') separator ', '),
              group_concat(ifnull(concat(projects.identifier, ' ', projects.title), '') separator ', ')
            )
          from tasks t2
          left join deals_tasks on deals_tasks.id_task = t2.id
          left join deals on deals.id = deals_tasks.id_deal
          left join projects_tasks on projects_tasks.id_task = t2.id
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
    switch ($this->router()->urlParamAsString('view')) {
      case 'briefOverview':
        $description->hide(['header', 'footer']);
        $description->showOnlyColumns(['identifier', 'title', 'id_main_developer', 'id_workflow_step', 'virt_worked']);
      break;
      default:
        $description->ui['addButtonText'] = 'Add Task';
        $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
        $description->hide(['footer']);

        $description->ui['filters'] = [
          'fTaskWorkflowStep' => Workflow::buildTableFilterForWorkflowSteps($this, 'Status'),
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
      break;
    }

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

    $mWorkflow = $this->getService(Workflow::class);
    list($defaultWorkflow, $idWorkflow, $idWorkflowStep) = $mWorkflow->getDefaultWorkflowInGroup('tasks');
    $savedRecord['id_workflow'] = $idWorkflow;
    $savedRecord['id_workflow_step'] = $idWorkflowStep;

    if (empty($savedRecord['identifier'])) {
      $savedRecord["identifier"] = '#' . $savedRecord["id"];
    }

    $this->record->recordUpdate($savedRecord);

    return parent::onAfterCreate($savedRecord);
  }

}
