<?php

namespace HubletoApp\Community\Customers\Models;

use HubletoApp\Community\Billing\Models\BillingAccount;
use HubletoApp\Community\Settings\Models\Country;
use HubletoApp\Community\Settings\Models\User;
use HubletoApp\Community\Deals\Models\Deal;
use HubletoApp\Community\Leads\Models\Lead;
use Illuminate\Database\Eloquent\Builder;

use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Text;
use \ADIOS\Core\Db\Column\Boolean;
use \ADIOS\Core\Db\Column\Date;

use HubletoApp\Community\Contacts\Models\Person;

class Customer extends \HubletoMain\Core\Model
{
  public string $table = 'customers';
  public string $eloquentClass = Eloquent\Customer::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public array $relations = [
    'PERSONS' => [ self::HAS_MANY, Person::class, 'id_customer' ],
    'COUNTRY' => [ self::HAS_ONE, Country::class, 'id', 'id_country' ],
    'USER' => [ self::BELONGS_TO, User::class, 'id_user', 'id' ],
    'FIRST_CONTACT' => [ self::HAS_ONE, Person::class, 'id_customer' ],
    //'BILLING_ACCOUNTS' => [ self::HAS_MANY, BillingAccount::class, 'id_customer', ],
    'ACTIVITIES' => [ self::HAS_MANY, CustomerActivity::class, 'id_customer', 'id' ],
    'DOCUMENTS' => [ self::HAS_MANY, CustomerDocument::class, 'id_customer', 'id'],
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
      'customer_id' => (new Varchar($this, $this->translate('Customer ID'))),
      'tax_id' => (new Varchar($this, $this->translate('Tax ID')))->setRequired(),
      'note' => (new Text($this, $this->translate('Notes'))),
      'date_created' => (new Date($this, $this->translate('Date Created')))->setReadonly()->setRequired(),
      'is_active' => (new Boolean($this, $this->translate('Active')))->setDefaultValue(1),
      'id_user' => (new Lookup($this, $this->translate('Assigned User'), User::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL')->setRequired()->setDefaultValue(1),
      'shared_folder' => new Varchar($this, "Shared folder (online document storage)"),
    ]);
  }

  public function indexes(array $indexes = []): array
  {
    return parent::indexes([
      /* "vat_id" => [
        "type" => "unique",
        "columns" => [
          "vat_id" => [
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
      ], */
      "tax_id" => [
        "type" => "unique",
        "columns" => [
          "tax_id" => [
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
    $description->ui['title'] = $this->translate('Customers');
    $description->ui['addButtonText'] = $this->translate('Add Customer');
    $description->ui['showHeader'] = true;
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
    $description->defaultValues['id_user'] = $this->main->auth->getUserId();
    $description->defaultValues['date_created'] = date("Y-m-d");

    return $description;
  }

  public function getNewRecordDataFromString(string $text): array {
    return [
      'name' => $text,
    ];
  }

  public function prepareLoadRecordQuery(array $includeRelations = [], int $maxRelationLevel = 0, mixed $query = null, int $level = 0): mixed
  {
    $query = parent::prepareLoadRecordQuery($includeRelations, 3);
    return $query;
  }

}
