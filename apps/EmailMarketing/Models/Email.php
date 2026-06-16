<?php

namespace Hubleto\App\Community\EmailMarketing\Models;


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

class Email extends \Hubleto\Erp\Model
{
  public string $table = 'email_marketing_emails';
  public string $recordManagerClass = RecordManagers\Email::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';
  public ?string $lookupUrlAdd = 'email-marketing/emails/add';
  public ?string $lookupUrlDetail = 'email-marketing/emails/{%ID%}';

  public array $relations = [
    'SENDER_ACCOUNT' => [ self::HAS_ONE, Account::class, 'id_sender_account', 'id' ],
    'LAUNCHED_BY' => [ self::BELONGS_TO, User::class, 'id_launched_by', 'id' ],
    'WORKFLOW' => [ self::HAS_ONE, Workflow::class, 'id', 'id_workflow' ],
    'WORKFLOW_STEP' => [ self::HAS_ONE, WorkflowStep::class, 'id', 'id_workflow_step' ],
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id'],
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id' ],
    'RECIPIENTS' => [ self::HAS_MANY, EmailRecipient::class, 'id_email', 'id' ],
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
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired()->setDefaultVisible()->setCssClass('font-bold')->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      'mail_subject' => (new Varchar($this, $this->translate('Subject')))->setCssClass('font-bold')->setDefaultVisible(),
      'mail_body' => (new Text($this, $this->translate('Body')))->setReactComponent('InputTextareaWithHtmlPreview'),
      'utm_source' => (new Varchar($this, $this->translate('UTM source')))->setDefaultVisible(),
      'utm_campaign' => (new Varchar($this, $this->translate('UTM campaign')))->setDefaultVisible(),
      'utm_term' => (new Varchar($this, $this->translate('UTM term')))->setDefaultVisible(),
      'utm_content' => (new Varchar($this, $this->translate('UTM content')))->setDefaultVisible(),
      'target_audience' => (new Text($this, $this->translate('Target audience')))->setDefaultVisible(),
      'goal' => (new Text($this, $this->translate('Goal')))->setDefaultVisible(),
      'notes' => (new Text($this, $this->translate('Notes'))),
      'color' => (new Color($this, $this->translate('Color')))->setIcon(self::COLUMN_COLOR_DEFAULT_ICON),
      'id_sender_account' => (new Lookup($this, $this->translate('Sender account'), Account::class)),
      'reply_to' => (new Varchar($this, $this->translate('Reply to'))),
      'id_workflow' => (new Lookup($this, $this->translate('Workflow'), Workflow::class))->setReadonly(),
      'id_workflow_step' => (new Lookup($this, $this->translate('Workflow step'), WorkflowStep::class))->setDefaultVisible()->setReadonly(),
      'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId()),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId())->setDefaultVisible(),
      'shared_with' => new Json($this, $this->translate('Shared with'))->setReactComponent('InputSharedWith')->setTableCellRenderer('TableCellRendererSharedWith'),
      'is_approved' => (new Boolean($this, $this->translate('Approved')))->setDefaultVisible(),
      'is_closed' => (new Boolean($this, $this->translate('Closed')))->setDefaultVisible(),
      'datetime_created' => (new DateTime($this, $this->translate('Created')))->setDefaultVisible()->setDefaultValue(date('Y-m-d H:i:s'))->setReadonly(),
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
    $description->ui['addButtonText'] = $this->translate('Add e-mail');

    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    $description->ui['filters'] = [
      'fEmailWorkflowStep' => Workflow::buildTableFilterForWorkflowSteps($this, $this->translate('Phase')),
      'fEmailOwnership' => [ 
        'title' => $this->translate('Ownership'), 
        'options' => [ 
          0 => $this->translate('All'),
          1 => $this->translate('Owned by me',), 
          2 => $this->translate('Managed by me') 
        ] 
      ],
      'fEmailClosed' => [
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
    list($defaultWorkflow, $idWorkflow, $idWorkflowStep) = $mWorkflow->getDefaultWorkflowInGroup('email_marketing_emails');
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
    $mail = $this->record->prepareReadQuery()->where($this->table . '.id', $recordId)->first();

    if (!$mail) return [];

    return [
      'Title' => $mail->title,
      'Target audience' => $mail->target_audience,
      'Mail subject' => $mail->mail_subject,
      'Mail body' => $mail->mail_body,
      'Goal' => $mail->goal,
      'Internal notes' => $mail->notes,
    ];
  }

}
