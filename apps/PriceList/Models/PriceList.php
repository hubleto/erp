<?php

namespace Hubleto\App\Community\PriceLists\Models;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Settings\Models\Currency;
use Hubleto\App\Community\Workflow\Models\Workflow;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;
use Hubleto\App\Community\PriceLists\Loader as PriceListApp;
use Hubleto\App\Community\PriceLists\Services\HistoryWriter;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Json;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Exceptions\RecordDeleteException;
use Hubleto\Framework\Exceptions\RecordSaveException;

class PriceList extends \Hubleto\Erp\Model
{
  public const TYPE_SALES = 1;
  public const TYPE_PURCHASE = 2;
  public const STATUS_DRAFT = 1;
  public const STATUS_ACTIVE = 2;
  public const STATUS_ARCHIVED = 3;
  public const BASIS_OWN_ITEMS = 1;
  public const BASIS_PRODUCT_SALES_PRICE = 2;
  public const BASIS_PRODUCT_PURCHASE_PRICE = 3;
  public const BASIS_PARENT_LIST = 4;
  public const DERIVE_NONE = 0;
  public const DERIVE_PERCENT_OFF = 1;
  public const DERIVE_PERCENT_ON = 2;
  public const DERIVE_AMOUNT_OFF = 3;
  public const DERIVE_AMOUNT_ON = 4;
  public const ROUNDING_NONE = 0;
  public const ROUNDING_NEAREST = 1;
  public const ROUNDING_UP = 2;
  public const ROUNDING_DOWN = 3;
  public const ROUNDING_ENDS_IN = 4;

  public string $table = 'price_lists';
  public string $recordManagerClass = RecordManagers\PriceList::class;
  public ?string $lookupSqlValue = 'concat(ifnull({%TABLE%}.code, ""), " ", ifnull({%TABLE%}.name, ""))';
  public ?string $lookupUrlAdd = 'price-lists/add';
  public ?string $lookupUrlDetail = 'price-lists/{%ID%}';

  public array $relations = [
    'CURRENCY' => [ self::BELONGS_TO, Currency::class, 'id_currency', 'id' ],
    'PARENT' => [ self::BELONGS_TO, PriceList::class, 'id_parent_price_list', 'id' ],
    'WORKFLOW' => [ self::BELONGS_TO, Workflow::class, 'id_workflow', 'id' ],
    'WORKFLOW_STEP' => [ self::BELONGS_TO, WorkflowStep::class, 'id_workflow_step', 'id' ],
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id' ],
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id' ],
    'ITEMS' => [ self::HAS_MANY, Item::class, 'id_price_list', 'id' ],
    'ASSIGNMENTS' => [ self::HAS_MANY, Assignment::class, 'id_price_list', 'id' ],
    'RULES' => [ self::HAS_MANY, Rule::class, 'id_price_list', 'id' ],
    'HISTORY' => [ self::HAS_MANY, History::class, 'id_price_list', 'id' ],
    'DOCUMENTS' => [ self::HAS_MANY, PriceListHasDocument::class, 'id_price_list', 'id' ],
  ];

  /**
   * Describes columns of the model.
   *
   * @return array
   *
   */
  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'code' => (new Varchar($this, $this->translate('Code')))->setDefaultVisible()->setCssClass('badge badge-info'),
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired()->setDefaultVisible()->setCssClass('font-bold'),
      'description' => (new Text($this, $this->translate('Description'))),
      'type' => (new Integer($this, $this->translate('Type')))->setDefaultVisible()->setDefaultValue(self::TYPE_SALES)->setEnumValues([self::TYPE_SALES => $this->translate('Sales'), self::TYPE_PURCHASE => $this->translate('Purchase')])->setEnumCssClasses([self::TYPE_SALES => 'bg-green-50', self::TYPE_PURCHASE => 'bg-blue-50']),
      'status' => (new Integer($this, $this->translate('Status')))->setDefaultVisible()->setDefaultValue(self::STATUS_DRAFT)->setEnumValues([self::STATUS_DRAFT => $this->translate('Draft'), self::STATUS_ACTIVE => $this->translate('Active'), self::STATUS_ARCHIVED => $this->translate('Archived')])->setEnumCssClasses([self::STATUS_DRAFT => 'bg-yellow-50', self::STATUS_ACTIVE => 'bg-green-50', self::STATUS_ARCHIVED => 'bg-gray-100']),
      'id_currency' => (new Lookup($this, $this->translate('Currency'), Currency::class))->setRequired()->setDefaultVisible(),
      'prices_include_vat' => (new Boolean($this, $this->translate('Prices include VAT'))),
      'basis' => (new Integer($this, $this->translate('Price basis')))->setDefaultValue(self::BASIS_OWN_ITEMS)->setEnumValues([self::BASIS_OWN_ITEMS => $this->translate('Own prices'), self::BASIS_PRODUCT_SALES_PRICE => $this->translate('Product sales price'), self::BASIS_PRODUCT_PURCHASE_PRICE => $this->translate('Product purchase price'), self::BASIS_PARENT_LIST => $this->translate('Parent price list')]),
      'id_parent_price_list' => (new Lookup($this, $this->translate('Parent price list'), PriceList::class))->setFkOnDelete('SET NULL'),
      'derive_method' => (new Integer($this, $this->translate('Derive method')))->setDefaultValue(self::DERIVE_NONE)->setEnumValues([self::DERIVE_NONE => $this->translate('None'), self::DERIVE_PERCENT_OFF => $this->translate('Percent off'), self::DERIVE_PERCENT_ON => $this->translate('Percent on'), self::DERIVE_AMOUNT_OFF => $this->translate('Amount off'), self::DERIVE_AMOUNT_ON => $this->translate('Amount on')]),
      'derive_value' => (new Decimal($this, $this->translate('Derive value'))),
      'rounding_policy' => (new Integer($this, $this->translate('Rounding policy')))->setDefaultValue(self::ROUNDING_NONE)->setEnumValues([self::ROUNDING_NONE => $this->translate('None'), self::ROUNDING_NEAREST => $this->translate('Nearest'), self::ROUNDING_UP => $this->translate('Up'), self::ROUNDING_DOWN => $this->translate('Down'), self::ROUNDING_ENDS_IN => $this->translate('Ends in')]),
      'rounding_step' => (new Decimal($this, $this->translate('Rounding step'))),
      'rounding_ending' => (new Decimal($this, $this->translate('Rounding ending'))),
      'valid_from' => (new Date($this, $this->translate('Valid from')))->setDefaultVisible(),
      'valid_to' => (new Date($this, $this->translate('Valid to')))->setDefaultVisible(),
      'priority' => (new Integer($this, $this->translate('Priority')))->setDefaultValue(0),
      'is_default' => (new Boolean($this, $this->translate('Is default')))->setDefaultVisible(),
      'id_workflow' => (new Lookup($this, $this->translate('Workflow'), Workflow::class))->setFkOnDelete('SET NULL')->setReadonly(),
      'id_workflow_step' => (new Lookup($this, $this->translate('Workflow step'), WorkflowStep::class))->setFkOnDelete('SET NULL')->setReadonly()->setDefaultVisible(),
      'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class))->setFkOnDelete('SET NULL')->setDefaultVisible()->setDefaultValue($this->authProvider()->getUserId())->setReactComponent('InputUserSelect'),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class))->setFkOnDelete('SET NULL')->setDefaultValue($this->authProvider()->getUserId())->setReactComponent('InputUserSelect'),
      'shared_with' => (new Json($this, $this->translate('Shared with')))->setReactComponent('InputSharedWith')->setTableCellRenderer('TableCellRendererSharedWith'),
      'note' => (new Text($this, $this->translate('Note'))),
    ]);
  }

  /**
   * Describes the table (list) of records.
   *
   * @return \Hubleto\Framework\Description\Table
   *
   */
  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = '';
    $description->ui['addButtonText'] = $this->translate('Add price list');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);

    $description->addFilter('fType', [
      'title' => $this->translate('Type'),
      'type' => 'multipleSelectButtons',
      'options' => [ 1 => $this->translate('Sales'), 2 => $this->translate('Purchase') ],
    ]);

    $description->addFilter('fStatus', [
      'title' => $this->translate('Status'),
      'type' => 'multipleSelectButtons',
      'options' => [ 1 => $this->translate('Draft'), 2 => $this->translate('Active'), 3 => $this->translate('Archived') ],
    ]);

    $description->addFilter('fValidNow', [
      'title' => $this->translate('Validity'),
      'type' => 'multipleSelectButtons',
      'options' => [ 1 => $this->translate('Valid now'), 2 => $this->translate('Not valid now') ],
    ]);

    return $description;
  }

  /**
   * Describes the form of a record.
   *
   * @return \Hubleto\Framework\Description\Form
   *
   */
  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();

    return $description;
  }

  /**
   * Validates uniqueness of the code, absence of cycles in derived lists and validity dates.
   *
   * @param array $record
   *
   * @return array
   *
   */
  public function onBeforeValidate(array $record): array
  {
    $record = parent::onBeforeValidate($record);

    $id = (int) ($record['id'] ?? 0);
    $code = trim((string) ($record['code'] ?? ''));

    if ($code !== '') {
      $query = $this->record->where($this->table . '.code', $code);
      if ($id > 0) $query = $query->where($this->table . '.id', '!=', $id);
      if ($query->count() > 0) {
        throw new RecordSaveException($this->translate('The code of a price list must be unique.'), ['code']);
      }
    }

    $currentId = (int) ($record['id_parent_price_list'] ?? 0);
    $visited = $id > 0 ? [$id => true] : [];
    while ($currentId > 0) {
      if (isset($visited[$currentId])) {
        throw new RecordSaveException(
          $this->translate('A price list cannot be derived from itself, directly or indirectly.'),
          ['id_parent_price_list']
        );
      }
      $visited[$currentId] = true;
      $parent = $this->record->where($this->table . '.id', $currentId)->first();
      $currentId = (int) ($parent->id_parent_price_list ?? 0);
    }

    $validFrom = (string) ($record['valid_from'] ?? '');
    $validTo = (string) ($record['valid_to'] ?? '');
    if ($validFrom !== '' && $validTo !== '' && $validFrom > $validTo) {
      throw new RecordSaveException(
        $this->translate('"Valid from" must not be later than "Valid to".'),
        ['valid_from', 'valid_to']
      );
    }

    return $record;
  }

  /**
   * Applies default workflow, generates the code and writes the first history record.
   *
   * @param array $savedRecord
   *
   * @return array
   *
   */
  public function onAfterCreate(array $savedRecord): array
  {
    $savedRecord = parent::onAfterCreate($savedRecord);

    /** @var Workflow $mWorkflow */
    $mWorkflow = $this->getModel(Workflow::class);
    $savedRecord = $mWorkflow->applyDefaultWorkflow($savedRecord, 'price-lists');

    if (empty($savedRecord['code'])) {
      $code = $this->config()->forApp(PriceListApp::class)->getAsString('numberingPattern', 'PL{YY}-{#}');
      $code = str_replace('{YYYY}', date('Y'), $code);
      $code = str_replace('{YY}', date('y'), $code);
      $code = str_replace('{MM}', date('m'), $code);
      $code = str_replace('{DD}', date('d'), $code);
      $code = str_replace('{#}', (string) $savedRecord['id'], $code);
      $savedRecord['code'] = $code;
    }

    $this->record->recordUpdate($savedRecord);
    $this->enforceSingleDefault($savedRecord);

    /** @var HistoryWriter $historyWriter */
    $historyWriter = $this->getService(HistoryWriter::class);
    $historyWriter->write((int) $savedRecord['id'], History::EVENT_CREATED, $this->translate('Price list created'));

    return $savedRecord;
  }

  /**
   * Keeps one default price list per type and currency and logs status changes.
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

    $this->enforceSingleDefault($savedRecord);

    $oldStatus = (int) ($originalRecord['status'] ?? 0);
    $newStatus = (int) ($savedRecord['status'] ?? 0);
    if ($oldStatus !== $newStatus) {
      $events = [
        self::STATUS_DRAFT => [History::EVENT_DRAFTED, $this->translate('Price list set to draft')],
        self::STATUS_ACTIVE => [History::EVENT_ACTIVATED, $this->translate('Price list activated')],
        self::STATUS_ARCHIVED => [History::EVENT_ARCHIVED, $this->translate('Price list archived')],
      ];
      if (isset($events[$newStatus])) {
        /** @var HistoryWriter $historyWriter */
        $historyWriter = $this->getService(HistoryWriter::class);
        $historyWriter->write((int) $savedRecord['id'], $events[$newStatus][0], $events[$newStatus][1]);
      }
    }

    return $savedRecord;
  }

  /**
   * An active price list cannot be deleted; it has to be archived first.
   *
   * @param int $id
   *
   * @return int
   *
   */
  public function onBeforeDelete(int $id): int
  {
    $priceList = $this->record->where($this->table . '.id', $id)->first();
    if ($priceList && (int) $priceList->status === self::STATUS_ACTIVE) {
      throw new RecordDeleteException($this->translate('An active price list cannot be deleted. Archive it first.'));
    }
    return parent::onBeforeDelete($id);
  }

  /**
   * Makes sure that at most one price list is the default one for the type and currency.
   *
   * @param array $record
   *
   * @return void
   *
   */
  protected function enforceSingleDefault(array $record): void
  {
    if (empty($record['is_default']) || empty($record['id'])) return;

    $this->record
      ->where($this->table . '.type', (int) ($record['type'] ?? self::TYPE_SALES))
      ->where($this->table . '.id_currency', (int) ($record['id_currency'] ?? 0))
      ->where($this->table . '.id', '!=', (int) $record['id'])
      ->where($this->table . '.is_default', 1)
      ->update(['is_default' => 0])
    ;
  }

  /**
   * Context for the AI assistant.
   *
   * @param int $sensitivityLevel
   * @param int $recordId
   *
   * @return array
   *
   */
  public function getAiAssistantContext(int $sensitivityLevel, int $recordId): array
  {
    $priceList = $this->record->where($this->table . '.id', $recordId)->first();
    if (!$priceList) return [];
    return [
      'code' => $priceList->code,
      'name' => $priceList->name,
      'type' => $priceList->type == self::TYPE_SALES ? 'sales' : 'purchase',
      'status' => $priceList->status,
      'valid_from' => $priceList->valid_from,
      'valid_to' => $priceList->valid_to,
    ];
  }

  /**
   * Loads data for the PDF of the price list, including products of its prices.
   *
   * @param int $recordId
   * @param array $relations
   *
   * @return array
   *
   */
  public function getDocumentPreviewVars(int $recordId, array $relations = []): array
  {
    return parent::getDocumentPreviewVars($recordId, array_merge($relations, ['ITEMS.PRODUCT']));
  }

  /**
   * Default template of the price list PDF.
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
        <title>{{ name }}</title>
        <style>
          * { font-family: Dejavu Sans; font-size: 10pt; }
          table { width: 100%; border-collapse: collapse; }
          th, td { border-bottom: 0.2mm solid #999999; padding: 2pt; text-align: left; }
        </style>
      </head>
      <body>
        <h1>{{ name }}</h1>
        <div>{{ code }} | {{ CURRENCY.code }} | {{ valid_from }} - {{ valid_to }}</div>
        <br/>
        <table>
          <tr><th>' . $this->translate('Product') . '</th><th>' . $this->translate('Min. quantity') . '</th><th>' . $this->translate('Value') . '</th></tr>
          {% for item in ITEMS %}
            <tr><td>{{ item.PRODUCT.name }}</td><td>{{ item.min_qty }}</td><td>{{ item.value }}</td></tr>
          {% endfor %}
        </table>
      </body>
      </html>
    ';
  }

}
