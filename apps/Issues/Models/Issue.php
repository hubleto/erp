<?php

namespace Hubleto\App\Community\Issues\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Json;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Text;

use Hubleto\App\Community\Workflow\Models\Workflow;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;
use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Customers\Models\Customer;

class Issue extends \Hubleto\Erp\Model
{
  public string $table = 'issues';
  public string $recordManagerClass = RecordManagers\Issue::class;
  public ?string $lookupSqlValue = 'concat({%TABLE%}.id, " ", {%TABLE%}.title)';
  public ?string $lookupUrlAdd = 'issues/add';
  public ?string $lookupUrlDetail = 'issues/{%ID%}';

  public array $relations = [
    'CUSTOMER' => [ self::HAS_ONE, Customer::class, 'id', 'id_customer'],
    'WORKFLOW' => [ self::HAS_ONE, Workflow::class, 'id', 'id_workflow' ],
    'WORKFLOW_STEP' => [ self::HAS_ONE, WorkflowStep::class, 'id', 'id_workflow_step' ],
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id'],
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired()->setIcon(self::COLUMN_NAME_DEFAULT_ICON)->setDefaultVisible(),
      'from' => (new Varchar($this, $this->translate('From')))->setDefaultVisible(),
      'description' => (new Text($this, $this->translate('Problem description')))->setRequired()->setDefaultVisible(),
      'notes' => (new Text($this, $this->translate('Notess'))),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class))->setIcon(self::COLUMN_ID_CUSTOMER_DEFAULT_ICON),
      'id_workflow' => (new Lookup($this, $this->translate('Workflow'), Workflow::class))->setReadonly(),
      'id_workflow_step' => (new Lookup($this, $this->translate('Workflow step'), WorkflowStep::class))->setDefaultVisible()->setReadonly(),
      'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId()),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId())->setDefaultVisible(),
      'shared_with' => new Json($this, $this->translate('Shared with'), User::class)->setReactComponent('InputSharedWith')->setTableCellRenderer('TableCellRendererSharedWith'),
      'is_closed' => (new Boolean($this, $this->translate('Closed')))->setDefaultVisible(),
      'thread_uid' => (new Varchar($this, $this->translate('Thread UID')))->setDefaultVisible(),
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

    $view = $this->router()->urlParamAsString("view");
    $filters = $this->router()->urlParamAsArray("filters");

    $description->ui['title'] = '';
    $description->ui['addButtonText'] = $this->translate("Add issue");

    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    $description->addFilter('fIssueWorkflowStep', Workflow::buildTableFilterForWorkflowSteps($this, 'Stage'));
    $description->addFilter('fIssueClosed', [
      'title' => $this->translate('Open / Closed'),
      'direction' => 'horizontal',
      'options' => [
        1 => $this->translate('Open'),
        2 => $this->translate('Closed'),
      ],
      'default' => 1,
    ]);

    $description->addFilter('fIssueOwnership', [ 
      'title' => $this->translate('Ownership'), 
      'options' => [ 
        0 => $this->translate('All'),
        1 => $this->translate('Owned by me',), 
        2 => $this->translate('Managed by me') 
      ] 
    ]);

    $description->addFilter('fIssueClosed', [
      'title' => $this->translate('Open / Closed'),
      'options' => [
        0 => $this->translate('Open'),
        1 => $this->translate('Closed'),
        2 => $this->translate('All'),
      ],
      'default' => 0,
    ]);

    $fCustomerOptions = [];
    foreach ($this->record->groupBy('id_customer')->with('CUSTOMER')->get() as $value) {
      if ($value->CUSTOMER) $fCustomerOptions[$value->id] = $value->CUSTOMER->name;
    }
    $description->addFilter('fIssueCustomer', [
      'title' => $this->translate('Customer'),
      'type' => 'multipleSelectButtons',
      'options' => $fCustomerOptions,
    ]);

    return $description;
  }

}
