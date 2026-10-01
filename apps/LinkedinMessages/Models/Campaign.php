<?php

namespace Hubleto\App\Community\LinkedinMessages\Models;

use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Virtual;
use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Workflow\Models\Workflow;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;

class Campaign extends \Hubleto\Erp\Model
{
  public string $table = 'linkedin_campaigns';
  public string $recordManagerClass = RecordManagers\Campaign::class;
  public ?string $lookupSqlValue = '`{%TABLE%}`.name';
  public ?string $lookupUrlDetail = 'linkedin-messages/campaigns/{%ID%}';

  public array $relations = [
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id' ],
    'WORKFLOW' => [ self::BELONGS_TO, Workflow::class, 'id_workflow', 'id' ],
    'WORKFLOW_STEP' => [ self::BELONGS_TO, WorkflowStep::class, 'id_workflow_step', 'id' ],
    'MESSAGES' => [ self::HAS_MANY, Message::class, 'id_campaign', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired()->setCssClass('font-bold')->setDefaultVisible(),
      'target' => (new Text($this, $this->translate('Target')))->setDefaultVisible(),
      'goal' => (new Text($this, $this->translate('Goal')))->setDefaultVisible(),
      'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setDefaultValue($this->authProvider()->getUserId()),
      'id_workflow' => (new Lookup($this, $this->translate('Workflow'), Workflow::class))->setReadonly(),
      'id_workflow_step' => (new Lookup($this, $this->translate('Workflow step'), WorkflowStep::class))->setDefaultVisible()->setReadonly(),
      'is_closed' => (new Boolean($this, $this->translate('Closed')))->setDefaultVisible()->setYesText($this->translate('Closed'))->setNoText(''),
      'date_created' => (new DateTime($this, $this->translate('Created')))->setReadonly()->setDefaultValue(date('Y-m-d H:i:s')),
      'virt_message_count' => (new Virtual($this, $this->translate('Messages')))->setDefaultVisible()
        ->setProperty('sql', "
          select count(*) from `linkedin_messages` `m` where `m`.`id_campaign` = `linkedin_campaigns`.`id`
        "),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    $description->ui['filters'] = [
      'fCampaignClosed' => [
        'direction' => 'horizontal',
        'options' => [
          1 => $this->translate('Open'),
          2 => $this->translate('Closed'),
          3 => $this->translate('All'),
        ],
        'default' => 1,
      ],
      'fCampaignWorkflowStep' => Workflow::buildTableFilterForWorkflowSteps($this, $this->translate('Step')),
    ];

    $description->ui['addButtonText'] = $this->translate('Add campaign');
    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();
    $description->ui['addButtonText'] = $this->translate('Add campaign');
    return $description;
  }

  public function getMaxReadLevelForLoadTableData(): int
  {
    return 1;
  }

  public function getMaxReadLevelForLoadFormData(): int
  {
    return 1;
  }

  public function onBeforeCreate(array $record): array
  {
    $record = parent::onBeforeCreate($record);
    if (empty($record['id_owner'])) {
      $record['id_owner'] = $this->authProvider()->getUserId();
    }

    /** @var Workflow */
    $mWorkflow = $this->getModel(Workflow::class);
    $record = $mWorkflow->applyDefaultWorkflow($record, 'linkedin-campaigns');

    return $record;
  }

  public function onBeforeDelete(int $id): int
  {
    // messages stay in the database, they are only detached from the campaign
    $mMessage = $this->getModel(Message::class);
    $mMessage->record->where('id_campaign', $id)->update(['id_campaign' => null]);
    return parent::onBeforeDelete($id);
  }
}
