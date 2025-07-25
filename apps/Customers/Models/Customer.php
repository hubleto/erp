<?php

namespace HubletoApp\Community\Customers\Models;

use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use HubletoApp\Community\Contacts\Models\Contact;
use HubletoApp\Community\Deals\Models\Deal;
use HubletoApp\Community\Leads\Models\Lead;
use HubletoApp\Community\Settings\Models\Country;
use HubletoApp\Community\Settings\Models\User;
use Hubleto\Framework\Helper;

class Customer extends \Hubleto\Framework\Models\Model
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
    'DOCUMENTS' => [ self::HAS_MANY, CustomerDocument::class, 'id_lookup', 'id'],
    'TAGS' => [ self::HAS_MANY, CustomerTag::class, 'id_customer', 'id' ],
    'LEADS' => [ self::HAS_MANY, Lead::class, 'id_customer', 'id'],
    'DEALS' => [ self::HAS_MANY, Deal::class, 'id_customer', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge([
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired()->setProperty('defaultVisibility', true),
      'street_line_1' => (new Varchar($this, $this->translate('Street Line 1'))),
      'street_line_2' => (new Varchar($this, $this->translate('Street Line 2'))),
      'region' => (new Varchar($this, $this->translate('Region'))),
      'city' => (new Varchar($this, $this->translate('City')))->setProperty('defaultVisibility', true),
      'postal_code' => (new Varchar($this, $this->translate('Postal Code'))),
      'id_country' => (new Lookup($this, $this->translate('Country'), Country::class)),
      'vat_id' => (new Varchar($this, $this->translate('VAT ID'))),
      'customer_id' => (new Varchar($this, $this->translate('Customer ID')))->setRequired()->setProperty('defaultVisibility', true),
      'tax_id' => (new Varchar($this, $this->translate('Tax ID'))),
      'note' => (new Text($this, $this->translate('Notes')))->setProperty('defaultVisibility', true),
      'date_created' => (new Date($this, $this->translate('Date Created')))->setReadonly()->setRequired()->setDefaultValue(date("Y-m-d")),
      'is_active' => (new Boolean($this, $this->translate('Active')))->setDefaultValue(false)->setProperty('defaultVisibility', true),
      'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class))->setRequired()->setDefaultValue($this->main->auth->getUserId())->setProperty('defaultVisibility', true),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class))->setRequired()->setDefaultValue($this->main->auth->getUserId())->setProperty('defaultVisibility', true),
      'shared_folder' => new Varchar($this, $this->translate("Shared folder (online document storage)")),
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

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = ''; //$this->translate('Customers');
    $description->ui['addButtonText'] = $this->translate('Add Customer');
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;
    $description->ui['showFooter'] = false;
    $description->columns['tags'] = ["title" => $this->translate("Tags")];

    $description->ui['defaultFilters'] = [
      'fArchive' => [ 'title' => $this->translate('Archive'), 'options' => [ 0 => $this->translate('Active'), 1 => $this->translate('Archived') ] ],
    ];

    return $description;
  }

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    $savedRecord = parent::onAfterUpdate($originalRecord, $savedRecord);

    if (isset($savedRecord["TAGS"])) {
      $helper = new Helper($this->main);
      $helper->deleteTags(
        array_column($savedRecord["TAGS"], "id"),
        "HubletoApp/Community/Customers/Models/CustomerTag",
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
