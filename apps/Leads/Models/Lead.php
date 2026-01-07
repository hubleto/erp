<?php

namespace Hubleto\App\Community\Leads\Models;


use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Virtual;
use Hubleto\App\Community\Contacts\Models\Contact;
use Hubleto\App\Community\Customers\Models\Customer;
use Hubleto\App\Community\Deals\Models\Deal;
use Hubleto\App\Community\Settings\Models\Currency;
use Hubleto\App\Community\Settings\Models\Setting;

use Hubleto\App\Community\Settings\Models\Team;
use Hubleto\Framework\Helper;
use Hubleto\App\Community\Workflow\Models\Workflow;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;
use Hubleto\App\Community\Auth\Models\User;

use Hubleto\App\Community\Settings\PermissionsManager;

use Hubleto\App\Community\Deals\Models\DealLead;

class Lead extends \Hubleto\Erp\Model
{
  public string $table = 'leads';
  public string $recordManagerClass = RecordManagers\Lead::class;
  public ?string $lookupSqlValue = '
    concat(
      "#",
      `{%TABLE%}`.id,
      " ",
      ifnull(
        (
          select concat(ifnull(`c`.`first_name`, ""), " ", ifnull(`c`.`last_name`, "")) from `contacts` `c`
          where `c`.`id` = `{%TABLE%}`.`id_contact`
        ),
        "---"
      ),
      " ",
      ifnull(
        (
          select group_concat(`cv`.`value` separator ", ") from `contact_values` `cv`
          where
            `cv`.`id_contact` = `{%TABLE%}`.`id_contact`
            and `type` in ("email", "other")
        ),
        "---"
      )
    )
  ';
  public ?string $lookupUrlDetail = 'leads/{%ID%}';

  public const STATUS_NO_INTERACTION_YET = 0;
  public const STATUS_CONTACTED = 1;
  public const STATUS_IN_PROGRESS = 2;
  public const STATUS_CLOSED = 3;
  public const STATUS_CONVERTED_TO_DEAL = 20;

  public array $relations = [
    'DEAL' => [ self::HAS_ONE, Deal::class, 'id_lead', 'id'],
    'CUSTOMER' => [ self::BELONGS_TO, Customer::class, 'id_customer', 'id' ],
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id'],
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id'],
    'TEAM' => [ self::BELONGS_TO, Team::class, 'id_team', 'id'],
    // 'LEVEL' => [ self::BELONGS_TO, Level::class, 'id_level', 'id'],
    'CONTACT' => [ self::HAS_ONE, Contact::class, 'id', 'id_contact'],
    'CURRENCY' => [ self::HAS_ONE, Currency::class, 'id', 'id_currency'],
    'WORKFLOW' => [ self::HAS_ONE, Workflow::class, 'id', 'id_workflow'],
    'WORKFLOW_STEP' => [ self::HAS_ONE, WorkflowStep::class, 'id', 'id_workflow_step'],

    'HISTORY' => [ self::HAS_MANY, LeadHistory::class, 'id_lead', 'id', ],
    'TAGS' => [ self::HAS_MANY, LeadTag::class, 'id_lead', 'id' ],
    'ACTIVITIES' => [ self::HAS_MANY, LeadActivity::class, 'id_lead', 'id' ],
    'DOCUMENTS' => [ self::HAS_MANY, LeadDocument::class, 'id_lead', 'id'],
    'CAMPAIGNS' => [ self::HAS_MANY, LeadCampaign::class, 'id_lead', 'id'],
    'TASKS' => [ self::HAS_MANY, LeadTask::class, 'id_deal', 'id'],
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
      // 'identifier' => (new Varchar($this, $this->translate('Identifier')))->setDefaultVisible(),
      'title' => (new Varchar($this, $this->translate('Specific subject (if any)')))->setCssClass('font-bold')->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class))->setDefaultValue($this->router()->urlParamAsInteger('idCustomer'))->setIcon(self::COLUMN_ID_CUSTOMER_DEFAULT_ICON),
      'virt_email' => (new Virtual($this, $this->translate('Email')))->setDefaultVisible()
        ->setProperty('sql', "select value from contact_values cv where cv.id_contact = leads.id_contact and cv.type = 'email' LIMIT 1")
      ,
      'virt_phone_number' => (new Virtual($this, $this->translate('Phone number')))
        ->setProperty('sql', "select value from contact_values cv where cv.id_contact = leads.id_contact and cv.type = 'number' LIMIT 1")
      ,
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class))->setRequired()->setDefaultValue(null)->setIcon(self::COLUMN_CONTACT_DEFAULT_ICON),
      // 'id_level' => (new Lookup($this, $this->translate('Level'), Level::class))->setDefaultVisible(),
      // 'status' => (new Integer($this, $this->translate('Status')))->setDefaultVisible()->setEnumValues(
      //   [
      //     $this::STATUS_NO_INTERACTION_YET => 'No interaction yet',
      //     $this::STATUS_CONTACTED => 'Contacted',
      //     $this::STATUS_IN_PROGRESS => 'In Progress',
      //     $this::STATUS_CLOSED => 'Closed',
      //     $this::STATUS_CONVERTED_TO_DEAL => 'Converted to deal',
      //   ]
      // )->setEnumCssClasses([
      //   self::STATUS_NO_INTERACTION_YET => 'bg-slate-100 text-slate-800',
      //   self::STATUS_CONTACTED => 'bg-blue-100 text-blue-800',
      //   self::STATUS_IN_PROGRESS => 'bg-yellow-100 text-yellow-800',
      //   self::STATUS_CLOSED => 'bg-slate-100 text-slate-800',
      //   self::STATUS_CONVERTED_TO_DEAL => 'bg-green-100 text-green-800',
      // ]),
      'price' => (new Decimal($this, $this->translate('Price')))->setDefaultValue(0),
      'id_currency' => (new Lookup($this, $this->translate('Currency'), Currency::class))->setReadonly(),
      'score' => (new Integer($this, $this->translate('Score')))->setDefaultVisible()->setColorScale('bg-light-blue-to-dark-blue'),
      'date_expected_close' => (new Date($this, $this->translate('Expected close date'))),
      'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId()),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId()),
      'id_team' => (new Lookup($this, $this->translate('Team'), Team::class)),
      'date_created' => (new DateTime($this, $this->translate('Created')))->setRequired()->setReadonly()->setDefaultValue(date("Y-m-d H:i:s")),
      'lost_reason' => (new Lookup($this, $this->translate("Reason for Lost"), LostReason::class)),
      'shared_folder' => new Varchar($this, "Online document folder"),
      'note' => (new Text($this, $this->translate('Notes')))->setDefaultVisible(),
      'id_workflow' => (new Lookup($this, $this->translate('Workflow'), Workflow::class)),
      'id_workflow_step' => (new Lookup($this, $this->translate('Workflow step'), WorkflowStep::class))->setDefaultVisible(),
      'source_channel' => (new Integer($this, $this->translate('Source channel')))->setEnumValues([
        1 => "Advertisement",
        2 => "Partner",
        3 => "Web",
        4 => "Cold call",
        5 => "E-mail",
        6 => "Refferal",
        7 => "Other",
      ]),
      'is_closed' => (new Boolean($this, $this->translate('Closed')))->setDefaultVisible(),
      'virt_tags' => (new Virtual($this, $this->translate('Tags')))->setDefaultVisible()
        ->setProperty('sql',"
          SELECT
            GROUP_CONCAT(DISTINCT lead_tags.name ORDER BY lead_tags.name SEPARATOR ', ')
          FROM `cross_lead_tags`
          INNER JOIN `lead_tags` ON `lead_tags`.`id` = `cross_lead_tags`.`id_tag`
          WHERE `cross_lead_tags`.`id_lead` = `leads`.`id`
        "),
    ]);
  }

  /**
   * [Description for describeInput]
   *
   * @param string $columnName
   *
   * @return \Hubleto\Framework\Description\Input
   *
   */
  public function describeInput(string $columnName): \Hubleto\Framework\Description\Input
  {
    $description = parent::describeInput($columnName);
    switch ($columnName) {
      case 'shared_folder':
        $description
          ->setReactComponent('InputHyperlink')
        ;
        break;
    }
    return $description;
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
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    $description->ui['filters'] = [
      'fLeadWorkflowStep' => Workflow::buildTableFilterForWorkflowSteps($this, 'Level'),
      'fLeadOwnership' => [ 'title' => 'Ownership', 'options' => [ 0 => 'All', 1 => 'Owned by me', 2 => 'Managed by me' ] ],
      'fLeadClosed' => [
        'title' => $this->translate('Open / Closed'),
        'options' => [
          0 => $this->translate('Open'),
          1 => $this->translate('Closed'),
          2 => $this->translate('All'),
        ],
        'default' => 0,
      ],
    ];

    $description->ui['addButtonText'] = $this->translate('Add lead');

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

    /** @var Setting */
    $mSettings = $this->getModel(Setting::class);

    $defaultCurrency = (int) $mSettings->record
      ->where("key", "Apps\Community\Settings\Currency\DefaultCurrency")
      ->first()
      ->value
    ;
    $description->defaultValues['id_currency'] = $defaultCurrency;

    $description->ui['addButtonText'] = $this->translate('Add Lead');

    return $description;
  }

  /**
   * [Description for getLookupDetails]
   *
   * @param array $dataRaw
   * 
   * @return string
   * 
   */
  public function getLookupDetails(array $dataRaw): string
  {
    return $dataRaw['CUSTOMER']['name'] ?? '';
  }

  /**
   * [Description for checkOwnership]
   *
   * @param array $record
   *
   * @return void
   *
   */
  public function checkOwnership(array $record): void
  {
    if (isset($record['id_customer']) && $record['id_customer'] && !isset($record['checkOwnership'])) {
      $mCustomer = $this->getService(Customer::class);
      $customer = $mCustomer->record
        ->where("id", (int) $record["id_customer"])
        ->first()
      ;

      // Dusan 30.5.2025: Toto robilo problemy, ked som pri testovani rucne zmenil ownera zaznamu.
      // if ($customer->id_owner != (int) $record["id_owner"]) {
      //   throw new \Exception("This lead cannot be assigned to the selected user,\nbecause they are not assigned to the selected customer.");
      // }
    }
  }

  public function getRelationsIncludedInLoadTableData(): array|null
  {
    return ['TAGS'];
  }

  public function getMaxReadLevelForLoadTableData(): int
  {
    return 1;
  }

  /**
   * [Description for onBeforeCreate]
   *
   * @param array $record
   *
   * @return array
   *
   */
  public function onBeforeCreate(array $record): array
  {
    $this->checkOwnership($record);
    return $record;
  }

  /**
   * [Description for onBeforeUpdate]
   *
   * @param array $record
   *
   * @return array
   *
   */
  public function onBeforeUpdate(array $record): array
  {
    $this->checkOwnership($record);

    $oldRecord = $this->record->find($record["id"])->toArray();
    $mLeadHistory = $this->getService(LeadHistory::class);

    $diff = $this->diffRecords($oldRecord, $record);
    $columns = $this->getColumns();
    foreach ($diff as $columnName => $values) {
      $oldValue = $values[0] ?? "None";
      $newValue = $values[1] ?? "None";

      if ($columns[$columnName]->getType() == "lookup") {
        $lookupModel = $this->getModel($columns[$columnName]->getLookupModel());
        $lookupSqlValue = $lookupModel->getLookupSqlValue($lookupModel->table);

        if ($oldValue != "None") {
          $oldValue = $lookupModel->record
            ->selectRaw($lookupSqlValue)
            ->where("id", $values[0])
            ->first()->toArray()
          ;
          $oldValue = reset($oldValue);
        }

        if ($newValue != "None") {
          $newValue = $lookupModel->record
            ->selectRaw($lookupSqlValue)
            ->where("id", $values[1])
            ->first()->toArray()
          ;
          $newValue = reset($newValue);
        }

        $mLeadHistory->record->recordCreate([
          "change_date" => date("Y-m-d"),
          "id_lead" => $record["id"],
          "description" => $columns[$columnName]->getTitle() . " changed from " . $oldValue . " to " . $newValue,
        ]);
      } else {
        if ($columns[$columnName]->getType() == "boolean") {
          $oldValue = $values[0] ? "Yes" : "No";
          $newValue = $values[1] ? "Yes" : "No";
        } elseif (!empty($columns[$columnName]->getEnumValues())) {
          $oldValue = $columns[$columnName]->getEnumValues()[$oldValue] ?? "None";
          $newValue = $columns[$columnName]->getEnumValues()[$newValue] ?? "None";
        }

        $mLeadHistory->record->recordCreate([
          "change_date" => date("Y-m-d"),
          "id_lead" => $record["id"],
          "description" => $columns[$columnName]->getTitle() . " changed from " . $oldValue . " to " . $newValue,
        ]);
      }
    }

    return $record;
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
    $savedRecord = parent::onAfterCreate($savedRecord);

    $mLeadHistory = $this->getService(LeadHistory::class);
    $mLeadHistory->record->recordCreate([
      "change_date" => date("Y-m-d"),
      "id_lead" => $savedRecord["id"],
      "description" => "Lead created"
    ]);

    $newLead = $savedRecord;

    /** @var Workflow */
    $mWorkflow = $this->getModel(Workflow::class);

    list($defaultWorkflow, $idWorkflow, $idWorkflowStep) = $mWorkflow->getDefaultWorkflowInGroup('leads');
    $newLead['id_workflow'] = $idWorkflow;
    $newLead['id_workflow_step'] = $idWorkflowStep;

    if (empty($newLead['identifier'])) {
      $newLead["identifier"] = $newLead["id"];
    }

    $this->record->recordUpdate($newLead);

    return $savedRecord;
  }

  /**
   * [Description for onAfterUpdate]
   *
   * @param array $originalRecord
   * @param array $savedRecord
   *
   * @return array
   *
   */
  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    $savedRecord = parent::onAfterUpdate($originalRecord, $savedRecord);

    if (isset($savedRecord["TAGS"])) {
      $helper = $this->getService(Helper::class);
      $helper->deleteTags(
        array_column($savedRecord["TAGS"], "id"),
        $this->getModel("Hubleto/App/Community/Leads/Models/LeadTag"),
        "id_lead",
        $savedRecord["id"]
      );
    }

    return $savedRecord;
  }
}
