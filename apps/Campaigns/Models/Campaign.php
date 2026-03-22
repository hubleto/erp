<?php

namespace Hubleto\App\Community\Campaigns\Models;


use Hubleto\App\Community\Mail\Models\Template;
use Hubleto\App\Community\Mail\Models\Account;
use Hubleto\Framework\Db\Column\Json;
use Hubleto\Framework\Db\Column\Color;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\App\Community\Workflow\Models\Workflow;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;
use Hubleto\App\Community\Auth\Models\User;

class Campaign extends \Hubleto\Erp\Model
{
  public string $table = 'campaigns';
  public string $recordManagerClass = RecordManagers\Campaign::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public array $relations = [
    'MAIL_ACCOUNT' => [ self::HAS_ONE, Account::class, 'id_mail_account', 'id' ],
    'MAIL_TEMPLATE' => [ self::HAS_ONE, Template::class, 'id_mail_template', 'id' ],
    'LAUNCHED_BY' => [ self::BELONGS_TO, User::class, 'id_launched_by', 'id' ],
    'WORKFLOW' => [ self::HAS_ONE, Workflow::class, 'id', 'id_workflow' ],
    'WORKFLOW_STEP' => [ self::HAS_ONE, WorkflowStep::class, 'id', 'id_workflow_step' ],
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id'],
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id' ],

    'RECIPIENTS' => [ self::HAS_MANY, Recipient::class, 'id_deal', 'id' ],
    'TASKS' => [ self::HAS_MANY, CampaignTask::class, 'id_deal', 'id' ],
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
      'uid' => (new Varchar($this, $this->translate('UID')))->setReadonly(true),
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired()->setDefaultVisible()->setCssClass('font-bold')->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      'type' => (new Integer($this, $this->translate('Type')))->setDefaultVisible()
        ->setEnumValues([
          0 => $this->translate('not specified'),
          1 => $this->translate('direct mail'),
          2 => $this->translate('cold call'),
          99 => $this->translate('other')
        ]
      ),
      'utm_source' => (new Varchar($this, $this->translate('UTM source')))->setDefaultVisible(),
      'utm_campaign' => (new Varchar($this, $this->translate('UTM campaign')))->setDefaultVisible(),
      'utm_term' => (new Varchar($this, $this->translate('UTM term')))->setDefaultVisible(),
      'utm_content' => (new Varchar($this, $this->translate('UTM content')))->setDefaultVisible(),
      'target_audience' => (new Text($this, $this->translate('Target audience')))->setDefaultVisible(),
      'goal' => (new Text($this, $this->translate('Goal')))->setDefaultVisible(),
      'notes' => (new Text($this, $this->translate('Notes'))),
      'shared_folder' => new Varchar($this, $this->translate("Online document folder"))->setReactComponent('InputHyperlink')->setCssClass('text-violet-800'),
      // 'mail_body' => (new Text($this, $this->translate('Mail body (HTML)')))->setReactComponent('InputWysiwyg'),
      'color' => (new Color($this, $this->translate('Color')))->setIcon(self::COLUMN_COLOR_DEFAULT_ICON),
      'id_mail_account' => (new Lookup($this, $this->translate('Mail account to send email from'), Account::class)),
      'id_mail_template' => (new Lookup($this, $this->translate('Mail template'), Template::class))
        ->setDefaultVisible()
      ,
      'reply_to' => (new Varchar($this, $this->translate('Reply to'))),
      'id_workflow' => (new Lookup($this, $this->translate('Workflow'), Workflow::class)),
      'id_workflow_step' => (new Lookup($this, $this->translate('Workflow step'), WorkflowStep::class))->setDefaultVisible(),
      'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId()),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId())->setDefaultVisible(),
      'shared_with' => new Json($this, $this->translate('Shared with'), User::class)->setReactComponent('InputSharedWith')->setTableCellRenderer('TableCellRendererSharedWith'),
      'is_approved' => (new Boolean($this, $this->translate('Approved')))->setDefaultVisible(),
      'is_closed' => (new Boolean($this, $this->translate('Closed')))->setDefaultVisible(),
      'datetime_created' => (new DateTime($this, $this->translate('Created')))->setDefaultVisible()->setDefaultValue(date('Y-m-d H:i:s')),
      'id_launched_by' => (new Lookup($this, $this->translate('Lanuched by'), User::class))->setReadonly(true),
      'datetime_launched' => (new DateTime($this, $this->translate('Launched')))->setReadonly(true)->setDefaultVisible(),
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

    $description->ui['title'] = '';
    $description->ui['addButtonText'] = $this->translate('Add Campaign');

    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    $description->ui['filters'] = [
      'fCampaignWorkflowStep' => Workflow::buildTableFilterForWorkflowSteps($this, $this->translate('Phase')),
      'fCampaignOwnership' => [ 
        'title' => $this->translate('Ownership'), 
        'options' => [ 
          0 => $this->translate('All'),
          1 => $this->translate('Owned by me',), 
          2 => $this->translate('Managed by me') 
        ] 
      ],
      'fCampaignClosed' => [
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
   * [Description for describeForm]
   *
   * @return \Hubleto\Framework\Description\Form
   * 
   */
  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();
    $description->show(['copyButton']);
    // $description->includeRelations = ['WORKFLOW', 'WORKFLOW_STEP'];

    return $description;
  }

  /**
   * [Description for getRelationsIncludedInLoadTableData]
   *
   * @return array|null
   * 
   */
  public function getRelationsIncludedInLoadTableData(): array|null
  {
    $recordId = $this->router()->urlParamAsInteger('recordId');
    if ($recordId > 0) return ['RECIPIENTS', 'RECIPIENTS.STATUS', 'RECIPIENTS.MAIL'];
    else return [];
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

    $savedRecord['uid'] = \Hubleto\Framework\Helper::generateUuidV4();
    $savedRecord['datetime_created'] = date("Y-m-d H:i:s");

    /** @var Workflow */
    $mWorkflow = $this->getModel(Workflow::class);
    list($defaultWorkflow, $idWorkflow, $idWorkflowStep) = $mWorkflow->getDefaultWorkflowInGroup('campaigns');
    $savedRecord['id_workflow'] = $idWorkflow;
    $savedRecord['id_workflow_step'] = $idWorkflowStep;

    $this->record->recordUpdate($savedRecord);

    return parent::onAfterCreate($savedRecord);
  }

  /**
   * [Description for getAiAssistantContext]
   *
   * @param int $sensitivityLevel
   * @param int $recordId
   * 
   * @return array
   * 
   */
  public function getAiAssistantContext(int $sensitivityLevel, int $recordId): array
  {
    $campaign = $this->record->prepareReadQuery()->where('campaigns.id', $recordId)->first();

    if (!$campaign) return [];

    return [
      'Campaing name' => $campaign->name,
      'Campaign type' => $campaign->type,
      'Target audience' => $campaign->target_audience,
      'Goal' => $campaign->goal,
      'Internal notes' => $campaign->notes,
    ];
  }

}
