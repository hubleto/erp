<?php

namespace HubletoApp\Community\Customers\Models;

use ADIOS\Core\Db\Column\Boolean;
use ADIOS\Core\Db\Column\Date;
use ADIOS\Core\Db\Column\Lookup;
use ADIOS\Core\Db\Column\Text;
use ADIOS\Core\Db\Column\Varchar;
use HubletoApp\Community\Contacts\Models\Contact;
use HubletoApp\Community\Deals\Models\Deal;
use HubletoApp\Community\Leads\Models\Lead;
use HubletoApp\Community\Settings\Models\Country;
use HubletoApp\Community\Settings\Models\User;
use HubletoMain\Core\Helper;

class Customer extends \HubletoMain\Core\Models\Model
{
  public string $table = 'customers';
  public string $recordManagerClass = RecordManagers\Customer::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';
  public ?string $lookupUrlDetail = 'customers/{%ID%}';
  public ?string $lookupUrlAdd = 'customers/add';

  public array $relations = [
    'CONTACTS' => [ self::HAS_MANY, Contact::class, 'id_customer' ],
    'COUNTRY' => [ self::HAS_ONE, Country::class, 'id', 'id_country' ],
    'OWNER' => [ self::BELONGS_TO, User::class, 'id_owner', 'id' ],
    'RESPONSIBLE' => [ self::BELONGS_TO, User::class, 'id_responsible', 'id' ],
    'ACTIVITIES' => [ self::HAS_MANY, CustomerActivity::class, 'id_customer', 'id' ],
    'DOCUMENTS' => [ self::HAS_MANY, CustomerDocument::class, 'id_lookup', 'id'],
    'TAGS' => [ self::HAS_MANY, CustomerTag::class, 'id_customer', 'id' ],
    'LEADS' => [ self::HAS_MANY, Lead::class, 'id_customer', 'id'],
    'DEALS' => [ self::HAS_MANY, Deal::class, 'id_customer', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired(),
      'street_line_1' => (new Varchar($this, $this->translate('Street Line 1'))),
      'street_line_2' => (new Varchar($this, $this->translate('Street Line 2'))),
      'region' => (new Varchar($this, $this->translate('Region'))),
      'city' => (new Varchar($this, $this->translate('City'))),
      'postal_code' => (new Varchar($this, $this->translate('Postal Code'))),
      'id_country' => (new Lookup($this, $this->translate('Country'), Country::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL'),
      'vat_id' => (new Varchar($this, $this->translate('VAT ID'))),
      'customer_id' => (new Varchar($this, $this->translate('Customer ID')))->setRequired(),
      'tax_id' => (new Varchar($this, $this->translate('Tax ID'))),
      'note' => (new Text($this, $this->translate('Notes'))),
      'date_created' => (new Date($this, $this->translate('Date Created')))->setReadonly()->setRequired(),
      'is_active' => (new Boolean($this, $this->translate('Active')))->setDefaultValue(1),
      'id_owner' => (new Lookup($this, $this->translate('Owner'), User::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL')->setRequired()->setDefaultValue(1),
      'id_responsible' => (new Lookup($this, $this->translate('Responsible'), User::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL')->setRequired()->setDefaultValue(1),
      'shared_folder' => new Varchar($this, "Shared folder (online document storage)"),
    ]);
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

  public function describeInput(string $columnName): \ADIOS\Core\Description\Input
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

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = ''; //$this->translate('Customers');
    $description->ui['addButtonText'] = $this->translate('Add Customer');
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showColumnSearch'] = true;
    $description->ui['showFooter'] = false;
    $description->columns['tags'] = ["title" => "Tags"];

    unset($description->columns['street_line_1']);
    unset($description->columns['street_line_2']);
    unset($description->columns['city']);
    unset($description->columns['postal_code']);
    unset($description->columns['region']);
    unset($description->columns['id_country']);
    unset($description->columns['note']);
    unset($description->columns['shared_folder']);

    //nadstavit aby bol is_active posledný
    $tempColumn = $description->columns['is_active'];
    unset($description->columns['is_active']);
    $description->columns['is_active'] = $tempColumn;


    return $description;
  }

  public function describeForm(): \ADIOS\Core\Description\Form
  {
    $description = parent::describeForm();

    $description->defaultValues['is_active'] = 0;
    $description->defaultValues['id_owner'] = $this->main->auth->getUserId();
    $description->defaultValues['id_responsible'] = $this->main->auth->getUserId();
    $description->defaultValues['date_created'] = date("Y-m-d");

    return $description;
  }

  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    if (isset($originalRecord["TAGS"])) {
      $helper = new Helper($this->main, $this->app);
      $helper->deleteTags(
        array_column($originalRecord["TAGS"], "id"),
        "HubletoApp/Community/Customers/Models/CustomerTag",
        "id_customer",
        $originalRecord["id"]
      );
    }
    return $savedRecord;
  }

  public function getNewRecordDataFromString(string $text): array {
    return [
      'name' => $text,
    ];
  }

}
