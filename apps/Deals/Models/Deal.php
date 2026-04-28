<?php

namespace Hubleto\App\Community\Deals\Models;


use DateTimeImmutable;

use Hubleto\App\Community\Deals\Loader as DealsApp;

use Hubleto\App\Community\Settings\PermissionsManager;
use Hubleto\Framework\Db\Column\Json;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\File;
use Hubleto\App\Community\Contacts\Models\Contact;
use Hubleto\App\Community\Customers\Models\Customer;
use Hubleto\App\Community\Leads\Models\Lead;
use Hubleto\App\Community\Settings\Models\Currency;
use Hubleto\App\Community\Workflow\Models\Workflow;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;
use Hubleto\App\Community\Settings\Models\Setting;
use Hubleto\App\Community\Auth\Models\User;

use Hubleto\Framework\Helper;

use Hubleto\App\Community\Documents\Models\Template;
use Hubleto\App\Community\Documents\Models\Document;

class Deal extends \Hubleto\Erp\Model
{
  public string $table = 'deals';
  public string $recordManagerClass = RecordManagers\Deal::class;
  public ?string $lookupSqlValue = 'concat(ifnull({%TABLE%}.identifier, ""), " ", ifnull({%TABLE%}.title, ""))';
  public ?string $lookupUrlDetail = 'deals/{%ID%}';

  public const RESULT_UNKNOWN = 0;
  public const RESULT_WON = 1;
  public const RESULT_LOST = 2;

  public const BUSINESS_TYPE_NEW = 1;
  public const BUSINESS_TYPE_EXISTING = 2;

  public const ENUM_SOURCE_CHANNELS = [
    1 => "Advertisement",
    2 => "Partner",
    3 => "Web",
    4 => "Cold call",
    5 => "E-mail",
    6 => "Referral",
    7 => "Other",
  ];

  public const ENUM_DEAL_RESULTS = [ self::RESULT_UNKNOWN => "Unknown", self::RESULT_WON => "Won", self::RESULT_LOST => "Lost" ];
  public const ENUM_BUSINESS_TYPES = [ self::BUSINESS_TYPE_NEW => "New", self::BUSINESS_TYPE_EXISTING => "Existing" ];

  public array $relations = [
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_lead', 'id'],
    'CUSTOMER' => [ self::BELONGS_TO, Customer::class, 'id_customer', 'id' ],
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id'],
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id'],
    'CONTACT' => [ self::HAS_ONE, Contact::class, 'id', 'id_contact'],
    'WORKFLOW' => [ self::HAS_ONE, Workflow::class, 'id', 'id_workflow'],
    'WORKFLOW_STEP' => [ self::HAS_ONE, WorkflowStep::class, 'id', 'id_workflow_step'],
    'CURRENCY' => [ self::HAS_ONE, Currency::class, 'id', 'id_currency'],
    'TEMPLATE_QUOTATION' => [ self::HAS_ONE, Template::class, 'id', 'id_template_quotation'],
    'TEMPLATE' => [ self::HAS_ONE, Template::class, 'id', 'id_template'],
    'DOCUMENT' => [ self::HAS_ONE, Document::class, 'id', 'id_document'],

    'HISTORY' => [ self::HAS_MANY, DealHistory::class, 'id_deal', 'id'],
    'TAGS' => [ self::HAS_MANY, DealTag::class, 'id_deal', 'id' ],
    'ITEMS' => [ self::HAS_MANY, Item::class, 'id_deal', 'id' ],
    'ACTIVITIES' => [ self::HAS_MANY, DealActivity::class, 'id_deal', 'id' ],
    'DOCUMENTS' => [ self::HAS_MANY, DealDocument::class, 'id_deal', 'id'],
    'LEADS' => [ self::HAS_MANY, DealLead::class, 'id_deal', 'id'],
    'TASKS' => [ self::HAS_MANY, DealTask::class, 'id_deal', 'id'],

    // 'ORDERS' => [ self::HAS_MANY, OrderDeal::class, 'id_deal', 'id'],
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
      'identifier' => (new Varchar($this, $this->translate('Deal Identifier')))->setCssClass('badge badge-info')->setDefaultVisible()->setIcon(self::COLUMN_IDENTIFIER_DEFUALT_ICON),
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired()->setDefaultVisible()->setCssClass('font-bold')->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class))->setDefaultValue($this->router()->urlParamAsInteger('idCustomer'))->setIcon(self::COLUMN_ID_CUSTOMER_DEFAULT_ICON),
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class))->setIcon(self::COLUMN_CONTACT_DEFAULT_ICON),
      'id_lead' => (new Lookup($this, $this->translate('Lead'), Lead::class))->setReadonly(),
      'version' => (new Integer($this, $this->translate('Version'))),
      // 'price' => (new Decimal($this, $this->translate('Price')))->setDecimals(2),
      'price_excl_vat' => new Decimal($this, $this->translate('Price excl. VAT')),
      'price_incl_vat' => new Decimal($this, $this->translate('Price incl. VAT')),
      'id_currency' => (new Lookup($this, $this->translate('Currency'), Currency::class))->setFkOnUpdate('RESTRICT')->setFkOnDelete('SET NULL')->setReadonly(),
      'date_expected_close' => (new Date($this, $this->translate('Expected close date'))),
      'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class))->setReactComponent('InputUserSelect')->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId()),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class))->setReactComponent('InputUserSelect')->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId()),
      'shared_with' => new Json($this, $this->translate('Shared with'), User::class)->setReactComponent('InputSharedWith')->setTableCellRenderer('TableCellRendererSharedWith'),
      'id_template_quotation' => (new Lookup($this, $this->translate('Template for quotation'), Template::class)),
      'customer_order_number' => (new Varchar($this, $this->translate('Customer\'s order number')))->setDefaultVisible(),
      'id_workflow' => (new Lookup($this, $this->translate('Workflow'), Workflow::class))->setReadonly(),
      'id_workflow_step' => (new Lookup($this, $this->translate('Workflow step'), WorkflowStep::class))->setDefaultVisible()->setReadonly(),
      'shared_folder' => new Varchar($this, $this->translate("Shared folder (online document storage)"))->setCssClass('text-violet-800'),
      'note' => (new Text($this, $this->translate('Notes'))),
      'source_channel' => (new Integer($this, $this->translate('Source channel')))->setEnumValues(array_map(fn($v) => $this->translate($v), self::ENUM_SOURCE_CHANNELS)),
      'description_before' => (new Text($this, $this->translate('Description/notes before the list of items'))),
      'description_after' => (new Text($this, $this->translate('Description/notes after the list of items'))),
      'is_closed' => (new Boolean($this, $this->translate('Closed')))->setDefaultVisible(),
      'deal_result' => (new Integer($this, $this->translate('Deal Result')))
        ->setEnumValues(array_map(fn($v) => $this->translate($v), self::ENUM_DEAL_RESULTS))
        ->setEnumCssClasses([
          self::RESULT_UNKNOWN => 'bg-yellow-100 text-yellow-800',
          self::RESULT_WON => 'bg-green-100 text-green-800',
          self::RESULT_LOST => 'bg-red-100 text-red-800',
        ])
        ->setDefaultValue(self::RESULT_UNKNOWN)
      ,
      'lost_reason' => (new Lookup($this, $this->translate("Reason for Lost"), LostReason::class)),
      'date_result_update' => (new DateTime($this, $this->translate('Date of result update')))->setReadonly(),
      'is_new_customer' => new Boolean($this, $this->translate('New Customer')),
      'business_type' => (new Integer($this, $this->translate('Business type')))
        ->setEnumValues(array_map(fn($v) => $this->translate($v), self::ENUM_BUSINESS_TYPES))
        ->setEnumCssClasses([
          self::BUSINESS_TYPE_NEW => 'bg-yellow-100 text-yellow-800',
          self::BUSINESS_TYPE_EXISTING => 'bg-blue-100 text-blue-800',
        ])
        ->setDefaultValue(self::BUSINESS_TYPE_NEW)
      ,
      'date_created' => (new DateTime($this, $this->translate('Created')))->setRequired()->setReadonly()->setDefaultValue(date("Y-m-d H:i:s")),
      'id_template' => (new Lookup($this, $this->translate('Template'), Template::class)),
      'id_document' => (new Lookup($this, $this->translate('Document'), Document::class)),
      'pdf' => (new File($this, $this->translate('PDF'))),
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
          ->setDescription($this->translate('Link to shared folder (online storage) with related documents'))
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
    $description->ui['addButtonText'] = $this->translate('Add Deal');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    $description->ui['filters'] = [
      'fDealWorkflowStep' => Workflow::buildTableFilterForWorkflowSteps($this, 'State'),
      'fDealSourceChannel' => [ 'title' => $this->translate('Source channel'), 'type' => 'multipleSelectButtons', 'options' => array_map(fn($v) => $this->translate($v), self::ENUM_SOURCE_CHANNELS) ],
      'fDealOwnership' => [ 'title' => $this->translate('Ownership'), 'options' => [ 0 => $this->translate('All'), 1 => $this->translate('Owned by me'), 2 => $this->translate('Managed by me') ] ],
      'fDealClosed' => [
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
    /** @var Setting */
    $mSettings = $this->getModel(Setting::class);

    $defaultCurrency = (int) $mSettings->record
      ->where("key", "Apps\Community\Settings\Currency\DefaultCurrency")
      ->first()
      ->value
    ;

    $description = parent::describeForm();
    $description->defaultValues['id_currency'] = $defaultCurrency;

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
   * [Description for getMaxReadLevelForLoadFormData]
   *
   * @return int
   * 
   */
  public function getMaxReadLevelForLoadFormData(): int
  {
    return 2;
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

    $mDealHistory = $this->getService(DealHistory::class);
    $mDealHistory->record->recordCreate([
      "change_date" => date("Y-m-d"),
      "id_deal" => $savedRecord["id"],
      "description" => $this->translate('Deal created')
    ]);

    if (empty($savedRecord['identifier'])) {

      $identifier = $this->config()->forApp(DealsApp::class)->getAsString('numberingPattern', 'D{YY}-{#}');
      $identifier = str_replace('{YYYY}', date('Y'), $identifier);
      $identifier = str_replace('{YY}', date('y'), $identifier);
      $identifier = str_replace('{MM}', date('m'), $identifier);
      $identifier = str_replace('{DD}', date('d'), $identifier);
      $identifier = str_replace('{#}', $savedRecord['id'], $identifier);

      $savedRecord["identifier"] = $identifier;
      $this->record->recordUpdate($savedRecord);
    }

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
        $this->getModel("Hubleto/App/Community/Deals/Models/DealTag"),
        "id_deal",
        $savedRecord["id"]
      );
    }

    $totalExclVat = 0;
    $totalInclVat = 0;

    $mItem = $this->getService(Item::class);
    $allItems = $mItem->record->where('id_deal', $savedRecord['id'])->get()->toArray();

    if (!empty($allItems)) {
      foreach ($allItems as $item) {
        if (!isset($item["_toBeDeleted_"])) {
          $totalExclVat += $item['price_excl_vat'];
          $totalInclVat += $item['price_incl_vat'];
        }
      }

      $this->record->find($savedRecord["id"])->update([
        "price_excl_vat" => $totalExclVat,
        "price_incl_vat" => $totalInclVat,
      ]);
    }

    return $savedRecord;
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
    $oldRecord = $this->record->find($record["id"])->toArray();
    $mDealHistory = $this->getService(DealHistory::class);

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

        $mDealHistory->record->recordCreate([
          "change_date" => date("Y-m-d"),
          "id_deal" => $record["id"],
          "description" => $columns[$columnName]->getTitle() . $this->translate(" changed from ") . $oldValue . $this->translate(" to ") . $newValue,
        ]);
      } else {
        if ($columns[$columnName]->getType() == "boolean") {
          $oldValue = $values[0] ? $this->translate("Yes") : $this->translate("No");
          $newValue = $values[1] ? $this->translate("Yes") : $this->translate("No");
        } elseif (!empty($columns[$columnName]->getEnumValues())) {
          $oldValue = $columns[$columnName]->getEnumValues()[$oldValue] ?? "None";
          $newValue = $columns[$columnName]->getEnumValues()[$newValue] ?? "None";
        }

        $mDealHistory->record->recordCreate([
          "change_date" => date("Y-m-d"),
          "id_deal" => $record["id"],
          "description" => $columns[$columnName]->getTitle() . $this->translate(" changed from ") . $oldValue . $this->translate(" to ") . $newValue,
        ]);
      }
    }

    return $record;
  }

  /**
   * [Description for getPreviewVars]
   *
   * @param int $idDeal
   * 
   * @return array
   * 
   */
  public function getPreviewVars(int $idDeal): array
  {
    /** @var Deal */
    $mDeal = $this->getModel(Deal::class);

    $deal = $mDeal->record->prepareReadQuery()->where('deals.id', $idDeal)->first();
    if (!$deal) throw new \Exception('Deal was not found.');

    $vars = $deal->toArray();
    $vars['hubleto'] = $this;
    $vars['user'] = $this->authProvider()->getUser();
    $vars['now'] = new \DateTimeImmutable()->format('Y-m-d H:i:s');

    unset($vars['CUSTOMER']['CONTACTS']);
    unset($vars['CUSTOMER']['OWNER']);
    unset($vars['CUSTOMER']['MANAGER']);
    unset($vars['CUSTOMER']['ACTIVITIES']);
    unset($vars['CUSTOMER']['DOCUMENTS']);
    unset($vars['CUSTOMER']['TAGS']);
    unset($vars['CUSTOMER']['LEADS']);
    unset($vars['CUSTOMER']['DEALS']);
    unset($vars['PROFILE']['COMPANY']);
    unset($vars['PROFILE']['TEMPLATE']);
    unset($vars['OWNER']['ROLES']);
    unset($vars['OWNER']['TEAMS']);
    unset($vars['OWNER']['DEFAULT_COMPANY']);
    unset($vars['MANAGER']['ROLES']);
    unset($vars['MANAGER']['TEAMS']);
    unset($vars['MANAGER']['DEFAULT_COMPANY']);
    unset($vars['WORKFLOW']);
    unset($vars['WORKFLOW_STEP']);
    unset($vars['TEMPLATE']);

    return $vars;

  }

  /**
   * [Description for getDocumentDefaultTemplate]
   *
   * @param array $vars
   * 
   * @return string
   * 
   */
  public function getDocumentDefaultTemplate(array $vars = []): string
  {
    return '
      <html>
      <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Ponuka {{ user.DEFAULT_COMPANY.name }} {{ number }}</title>
        <style>
          {% if PDF_EXPORT %}
            * { font-family: Dejavu Sans !important; line-height: 0.95em; font-size: 10pt; }
          {% else %}
            * { font-family: Verdana !important; font-size: 10pt; }
          {% endif %} 
          .fbigger { font-size: 12pt; }
          table { width: 100%; border-collapse: collapse; }
          table td { vertical-align: top; }
          .dmain { border: 0.2mm solid #444444; margin-top: 0.5em; }
          .dcolumn { width: 50%; float: left; }
          .content { padding: 4pt; }
          .odberatel { border: 0.5mm solid #444444; }
          .dodod_info { color: #777; text-transform: uppercase; padding-bottom: 2mm; text-align: left; }
          .dodod_info span { border-bottom: 0.2mm solid #777; font-size: 8pt; font-style: italic; }
          .faktura_info { padding: 16pt 2pt 6pt 2pt; text-align: left; }
          table.polozky td { padding: 0.8pt; font-size: 9pt; vertical-align: middle; }
          table.polozky tr.header td { font-weight: bold; border-bottom: 0px solid #777; }
          table.polozky tr.sep td { width: 100%; height: 4pt; }
          .sumar { width: 100%; float: right; }
          .sumar table td { padding: 0.8pt; vertical-align: middle; }
          .sumar table tr.sep td { width: 100%; height: 10pt; }
          .sumar table tr.suma td { border-top: 0.2mm solid #777; padding-top: 1mm; }
          .text-right { text-align: right; }
        </style>
      </head>
      <body>
        <!-- TOP -->
        <div>Deal # <b>{{ identifier }}</b></div>
        <br/>
        <div class="dmain">
          <div class="dcolumn">
            <div class="content">
              <div>
                <b>SUPPLIER</b><br/>
                <br/>
                <b>{{ user.DEFAULT_COMPANY.name }}</b><br/>
                {{ user.DEFAULT_COMPANY.street_1 }}<br/>
                {% if user.DEFAULT_COMPANY.street_2 %} {{ user.DEFAULT_COMPANY.street_2 }}<br/> {% endif %}
                {{ user.DEFAULT_COMPANY.zip }} {{ user.DEFAULT_COMPANY.city }}<br/>
                {% if user.DEFAULT_COMPANY.country %} {{ user.DEFAULT_COMPANY.country }}<br/> {% endif %}
                <br/>
                {% if user.DEFAULT_COMPANY.registration_id %} IČO: {{ user.DEFAULT_COMPANY.registration_id }}<br/> {% endif %}
                {% if user.DEFAULT_COMPANY.tax_id %} DIČ: {{ user.DEFAULT_COMPANY.tax_id }}<br/> {% endif %}
                {% if user.DEFAULT_COMPANY.vat_id %} IČDPH: {{ user.DEFAULT_COMPANY.vat_id }}<br/> {% endif %}
                <br/>
                {{ user.DEFAULT_COMPANY.business_register }}<br/>
              </div>
            </div>
          </div>
          <div class="dcolumn">
            <div class="content odberatel">
              <b>CUSTOMER</b><br/>
              <br/>
              <b>{{ CUSTOMER.name }}</b><br/>
              {{ CUSTOMER.street_line_1 }}<br/>
              {% if CUSTOMER.street_line_2 %} {{ CUSTOMER.street_line_2 }}<br/> {% endif %}
              {% if CUSTOMER.region %} {{ CUSTOMER.region }}<br/> {% endif %}
              {{ CUSTOMER.postal_code }} {{ CUSTOMER.city }}<br/>
              {% if CUSTOMER.country %} {{ CUSTOMER.country }}<br/> {% endif %}
              <br/>
              {% if CUSTOMER.customer_id %} IČO: {{ CUSTOMER.customer_id }}<br/> {% endif %}
              {% if CUSTOMER.tax_id %} DIČ: {{ CUSTOMER.tax_id }}<br/> {% endif %}
              {% if CUSTOMER.vat_id %} IČDPH: {{ CUSTOMER.vat_id }}<br/> {% endif %}
            </div>
          </div>
          <div style="clear:both;"></div>
        </div>

        {% if description_before %}
          <div style="text-align:left;margin:1em 0em;color:blue">{{ description_before }}</div>
        {% endif %}

        <div class="dmain">
          <div class="content">
            <table class="polozky">
              <thead>
                <tr class="header" style="background:#EEEEEE">
                  <td style="width:100%;padding:5pt 5pt 5pt 5pt">
                    <table style="width:100%">
                      <tbody>
                        <tr>
                          <td style="width:50%">Item</td>
                          <td style="width:50%;text-align:right">Unit price × Amount = Price</td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                </tr>
              </thead>
              <tbody>
                {% for i in ITEMS %}
                  <tr>
                    <td style="width:100%;">
                      <table style="width:100%">
                        <tbody>
                          <tr>
                            <td style="width:50%;color:blue">
                              <i>{{ i.title }}</i>
                              <div style="color:#444444;font-size:8pt">{{ i.description }}<div>
                            </td>
                            <td style="width:50%;text-align:right">
                              <span>{{ hubleto.locale().formatCurrency(i.unit_price * (100 - i.discount)/100, CURRENCY.symbol) }}</span>
                              ×
                              {{ hubleto.locale().formatNumber(i.amount, 4) }}
                              = <b><span>{{ hubleto.locale().formatCurrency(i.price_excl_vat, CURRENCY.symbol) }}</span> excl. VAT</b>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </td>
                  </tr>
                {% endfor %}
              </tbody>
            </table>
          </div>
        </div>

        {% if description_after %}
          <div style="text-align:left;margin:1em 0em;white-space:pre-wrap;">{{ description_after }}</div>
        {% endif %}

        <br/>
        <br/>
        Issued by {{ user.first_name }} {{ user.last_name }} {{ now }}.<br/>
        Generated in ERP Hubleto.
        https://www.hubleto.eu<br/>
      </body>
    ';
  }

}
