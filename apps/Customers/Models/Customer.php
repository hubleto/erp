<?php

namespace Hubleto\App\Community\Customers\Models;



use Hubleto\Erp\Model;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Contacts\Models\Contact;
use Hubleto\App\Community\Deals\Models\Deal;
use Hubleto\App\Community\Leads\Models\Lead;
use Hubleto\App\Community\Settings\Models\Country;
use Hubleto\Framework\Db\Column\Virtual;
use Hubleto\Framework\Description\Input;
use Hubleto\Framework\Description\Table;
use Hubleto\Framework\Helper;

class Customer extends Model
{
  public bool $isExtendableByCustomColumns = true;

  public string $table = 'customers';
  public string $recordManagerClass = RecordManagers\Customer::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';
  public ?string $lookupUrlDetail = 'customers/{%ID%}';
  public ?string $lookupUrlAdd = 'customers/add';

  public array $relations = [
    'CONTACTS' => [ self::HAS_MANY, Contact::class, 'id_customer' ],
    'COUNTRY' => [ self::HAS_ONE, Country::class, 'id', 'id_country' ],
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id' ],
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id' ],
    'ACTIVITIES' => [ self::HAS_MANY, CustomerActivity::class, 'id_customer', 'id' ],
    'DOCUMENTS' => [ self::HAS_MANY, CustomerDocument::class, 'id_customer', 'id'],
    'TAGS' => [ self::HAS_MANY, CustomerTag::class, 'id_customer', 'id' ],
    'LEADS' => [ self::HAS_MANY, Lead::class, 'id_customer', 'id'],
    'DEALS' => [ self::HAS_MANY, Deal::class, 'id_customer', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(
      [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired()->setDefaultVisible()->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      'street_line_1' => (new Varchar($this, $this->translate('Street Line 1'))),
      'street_line_2' => (new Varchar($this, $this->translate('Street Line 2'))),
      'region' => (new Varchar($this, $this->translate('Region'))),
      'city' => (new Varchar($this, $this->translate('City')))->setDefaultVisible(),
      'postal_code' => (new Varchar($this, $this->translate('Postal Code'))),
      'id_country' => (new Lookup($this, $this->translate('Country'), Country::class)),
      'vat_id' => (new Varchar($this, $this->translate('VAT ID'))),
      'customer_id' => (new Varchar($this, $this->translate('Customer ID')))->setRequired()->setDefaultVisible(),
      'tax_id' => (new Varchar($this, $this->translate('Tax ID'))),
      'note' => (new Text($this, $this->translate('Notes')))->setDefaultVisible(),
      'date_created' => (new Date($this, $this->translate('Date Created')))->setReadonly()->setRequired()->setDefaultValue(date("Y-m-d")),
      'is_active' => (new Boolean($this, $this->translate('Active')))->setDefaultValue(false)->setDefaultVisible(),
      'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class))->setReactComponent('InputUserSelect')->setRequired()->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId())->setDefaultVisible(),
      'id_manager' => new Lookup($this, $this->translate('Manager'), User::class)->setReactComponent('InputUserSelect')->setRequired()->setDefaultValue($this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId())->setDefaultVisible(),
      'shared_folder' => new Varchar($this, $this->translate("Shared folder (online document storage)")),
      'virt_tags' => (new Virtual($this, $this->translate('Tags')))->setDefaultVisible()
        ->setProperty('sql',"
          SELECT
            GROUP_CONCAT(DISTINCT customer_tags.name ORDER BY customer_tags.name SEPARATOR ', ')
          FROM `cross_customer_tags`
          INNER JOIN `customer_tags` ON `customer_tags`.`id` = `cross_customer_tags`.`id_tag`
          WHERE `cross_customer_tags`.`id_customer` = `customers`.`id`
        "),

    ], parent::describeColumns());
  }

  public function indexes(array $indexes = []): array
  {
    return parent::indexes([
      "name" => [
        "type" => "unique",
        "columns" => [
          "name" => [
            "order" => "asc",
          ],
        ],
      ],
      "customer_id" => [
        "type" => "unique",
        "columns" => [
          "customer_id" => [
            "order" => "asc",
          ],
        ],
      ],
    ]);
  }

  public function describeInput(string $columnName): Input
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

  public function describeTable(): Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = ''; //$this->translate('Customers');
    $description->ui['addButtonText'] = $this->translate('Add Customer');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    $description->ui['filters'] = [
      'fArchive' => [ 'title' => $this->translate('Archive'), 'options' => [ 0 => $this->translate('Active'), 1 => $this->translate('Archived') ] ],
    ];

    return $description;
  }

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    $savedRecord = parent::onAfterUpdate($originalRecord, $savedRecord);

    if (isset($savedRecord["TAGS"])) {
      $helper = $this->getService(Helper::class);
      $helper->deleteTags(
        array_column($savedRecord["TAGS"], "id"),
        $this->getModel("Hubleto/App/Community/Customers/Models/CustomerTag"),
        "id_customer",
        $savedRecord["id"]
      );
    }
    return $savedRecord;
  }

  public function getNewRecordDataFromString(string $text): array
  {
    return [
      'name' => $text,
    ];
  }

}
