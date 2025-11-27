<?php

namespace Hubleto\App\Community\Projects\Models;


use Hubleto\App\Community\Projects\Loader as ProjectsApp;

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
use Hubleto\App\Community\Deals\Models\Deal;

use Hubleto\App\Community\Workflow\Models\Workflow;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;
use Hubleto\App\Community\Contacts\Models\Contact;
use Hubleto\App\Community\Customers\Models\Customer;
use Hubleto\App\Community\Auth\Models\User;

class Project extends \Hubleto\Erp\Model
{
  public string $table = 'projects';
  public string $recordManagerClass = RecordManagers\Project::class;
  public ?string $lookupSqlValue = 'concat(ifnull({%TABLE%}.identifier, ""), " ", ifnull({%TABLE%}.title, ""))';
  public ?string $lookupUrlDetail = 'projects/{%ID%}';

  public array $relations = [
    'MAIN_DEVELOPER' => [ self::HAS_ONE, User::class, 'id_main_developer', 'id' ],
    'ACCOUNT_MANAGER' => [ self::HAS_ONE, User::class, 'id_account_manager', 'id' ],
    'CUSTOMER' => [ self::HAS_ONE, Customer::class, 'id_customer', 'id' ],
    'CONTACT' => [ self::HAS_ONE, Contact::class, 'id_contact', 'id' ],
    'PHASE' => [ self::HAS_ONE, Phase::class, 'id_phase', 'id' ],
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id' ],
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id' ],

    'ORDERS' => [ self::HAS_MANY, ProjectOrder::class, 'id_order', 'id'],
    'TASKS' => [ self::HAS_MANY, ProjectTask::class, 'id_task', 'id'],
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
      'id_deal' => (new Lookup($this, $this->translate('Deal'), Deal::class))->setDefaultHidden(),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class))->setIcon(self::COLUMN_ID_CUSTOMER_DEFAULT_ICON),
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class))->setDefaultHidden()->setIcon(self::COLUMN_CONTACT_DEFAULT_ICON),
      'identifier' => (new Varchar($this, $this->translate('Identifier')))->setDefaultVisible()->setCssClass('badge badge-info')->setDescription('Leave empty to generate automatically.')->setIcon(self::COLUMN_IDENTIFIER_DEFUALT_ICON),
      'title' => (new Varchar($this, $this->translate('Title')))->setDefaultVisible()->setRequired()->setCssClass('font-bold')->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      'description' => (new Text($this, $this->translate('Description'))),
      'id_main_developer' => (new Lookup($this, $this->translate('Main developer'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setRequired()
        ->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId())
      ,
      'id_account_manager' => (new Lookup($this, $this->translate('Account manager'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setRequired()
        ->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId())
      ,
      'priority' => (new Integer($this, $this->translate('Priority'))),
      'date_start' => (new Date($this, $this->translate('Start')))->setDefaultValue(date("Y-m-d")),
      'date_deadline' => (new Date($this, $this->translate('Deadline')))->setDefaultValue(date("Y-m-d")),
      'average_hourly_costs' => (new Decimal($this, $this->translate('Average hourly costs')))->setDefaultVisible()->setUnit('€'),
      'budget' => (new Decimal($this, $this->translate('Budget')))->setDefaultVisible()->setUnit('€'),
      'id_workflow' => (new Lookup($this, $this->translate('Workflow'), Workflow::class)),
      'id_workflow_step' => (new Lookup($this, $this->translate('Workflow step'), WorkflowStep::class))->setDefaultVisible(),
      'is_closed' => (new Boolean($this, $this->translate('Closed')))->setDefaultVisible(),
      'color' => (new Color($this, $this->translate('Color')))->setDefaultVisible()->setIcon(self::COLUMN_COLOR_DEFAULT_ICON),
      'online_documentation_folder' => (new Varchar($this, "Online documentation folder"))->setReactComponent('InputHyperlink'),
      'notes' => (new Text($this, $this->translate('Notes'))),
      'date_created' => (new DateTime($this, $this->translate('Created')))->setReadonly()->setDefaultValue(date("Y-m-d H:i:s")),
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

    $view = $this->router()->urlParamAsString('view');

    switch ($view) {
      case 'briefOverview':
        $description->showOnlyColumns(['identifier', 'title', 'id_main_developer', 'id_account_manager', 'id_workflow_step']);
      break;
      default:
        $description->ui['addButtonText'] = 'Add Project';
        $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
        $description->hide(['footer']);

        $description->ui['filters'] = [
          'fProjectWorkflowStep' => Workflow::buildTableFilterForWorkflowSteps($this, 'Phase'),
          'fProjectClosed' => [
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

    /** @var Workflow */
    $mWorkflow = $this->getModel(Workflow::class);

    list($defaultWorkflow, $idWorkflow, $idWorkflowStep) = $mWorkflow->getDefaultWorkflowInGroup('projects');
    $savedRecord['id_workflow'] = $idWorkflow;
    $savedRecord['id_workflow_step'] = $idWorkflowStep;

    if (empty($savedRecord['identifier'])) {

      $identifier = $this->config()->forApp(ProjectsApp::class)->getAsString('numberingPattern', 'P{YY}-{#}');
      $identifier = str_replace('{YYYY}', date('Y'), $identifier);
      $identifier = str_replace('{YY}', date('y'), $identifier);
      $identifier = str_replace('{MM}', date('m'), $identifier);
      $identifier = str_replace('{DD}', date('d'), $identifier);
      $identifier = str_replace('{#}', $savedRecord['id'], $identifier);

      $savedRecord["identifier"] = $identifier;
      $this->record->recordUpdate($savedRecord);
    }

    $this->record->recordUpdate($savedRecord);

    return parent::onAfterCreate($savedRecord);
  }

}
