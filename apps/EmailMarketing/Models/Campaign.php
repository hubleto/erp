<?php

namespace Hubleto\App\Community\EmailMarketing\Models;


use Hubleto\App\Community\Mail\Models\Template;
use Hubleto\Framework\Helper;
use Hubleto\Framework\Db\Column\Json;
use Hubleto\Framework\Db\Column\Color;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Virtual;
use Hubleto\App\Community\Workflow\Models\Workflow;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;
use Hubleto\App\Community\Auth\Models\User;

class Campaign extends \Hubleto\Erp\Model
{
  public string $table = 'email_marketing_campaigns';
  public string $recordManagerClass = RecordManagers\Campaign::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';
  public ?string $lookupUrlAdd = 'email-marketing/campaigns/add';
  public ?string $lookupUrlDetail = 'email-marketing/campaigns/{%ID%}';

  public array $relations = [
    'WORKFLOW' => [ self::HAS_ONE, Workflow::class, 'id', 'id_workflow' ],
    'WORKFLOW_STEP' => [ self::HAS_ONE, WorkflowStep::class, 'id', 'id_workflow_step' ],
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id'],
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id' ],
    'SCHEDULES' => [ self::HAS_MANY, CampaignSchedule::class, 'id_campaign', 'id' ],
    'TAGS' => [ self::HAS_MANY, CampaignTag::class, 'id_campaign', 'id' ],
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
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired()->setDefaultVisible()->setCssClass('font-bold')->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      'target_audience' => (new Text($this, $this->translate('Target audience')))->setDefaultVisible(),
      'goal' => (new Text($this, $this->translate('Goal')))->setDefaultVisible(),
      'notes' => (new Text($this, $this->translate('Notes'))),
      'color' => (new Color($this, $this->translate('Color')))->setIcon(self::COLUMN_COLOR_DEFAULT_ICON),
      'id_workflow' => (new Lookup($this, $this->translate('Workflow'), Workflow::class))->setReadonly(),
      'id_workflow_step' => (new Lookup($this, $this->translate('Workflow step'), WorkflowStep::class))->setDefaultVisible()->setReadonly(),
      'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId()),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId())->setDefaultVisible(),
      'is_closed' => (new Boolean($this, $this->translate('Closed')))->setDefaultVisible(),
      'shared_with' => new Json($this, $this->translate('Shared with'))->setReactComponent('InputSharedWith')->setTableCellRenderer('TableCellRendererSharedWith'),
      'virt_tags' => (new Virtual($this, $this->translate('Tags')))->setDefaultVisible()
        ->setProperty('sql',"
          SELECT
            GROUP_CONCAT(DISTINCT email_marketing_tags.name ORDER BY email_marketing_tags.name SEPARATOR ', ')
          FROM `email_marketing_campaign_tags`
          INNER join `email_marketing_tags` ON `email_marketing_tags`.`id` = `email_marketing_campaign_tags`.`id_tag`
          WHERE `email_marketing_campaign_tags`.`id_campaign` = `email_marketing_campaigns`.`id`
        "),
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
    $description->ui['addButtonText'] = $this->translate('Add campaign');

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
    $relations = ['TAGS', 'TAGS.TAG'];
    if ($recordId > 0) $relations[] = ['EMAILS'];
    return $relations;
  }

  public function getMaxReadLevelForLoadTableData(): int
  {
    return 3;
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
    list($defaultWorkflow, $idWorkflow, $idWorkflowStep) = $mWorkflow->getDefaultWorkflowInGroup('email_marketing_emails');
    $savedRecord['id_workflow'] = $idWorkflow;
    $savedRecord['id_workflow_step'] = $idWorkflowStep;

    $this->record->recordUpdate($savedRecord);

    return parent::onAfterCreate($savedRecord);
  }

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    $savedRecord = parent::onAfterUpdate($originalRecord, $savedRecord);

    if (isset($savedRecord["TAGS"])) {
      $helper = $this->getService(Helper::class);
      $helper->deleteTags(
        array_column($savedRecord["TAGS"], "id"),
        $this->getModel("Hubleto/App/Community/EmailMarketing/Models/CampaignTag"),
        "id_campaign",
        $savedRecord["id"]
      );
    }

    return $savedRecord;
  }
}
