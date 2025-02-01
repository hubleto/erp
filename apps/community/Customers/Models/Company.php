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

class Company extends \HubletoMain\Core\Model
{
  public string $table = 'companies';
  public string $eloquentClass = Eloquent\Company::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public array $relations = [
    'PERSONS' => [ self::HAS_MANY, Person::class, 'id_company' ],
    'COUNTRY' => [ self::HAS_ONE, Country::class, 'id', 'id_country' ],
    'USER' => [ self::BELONGS_TO, User::class, 'id_user', 'id' ],
    'FIRST_CONTACT' => [ self::HAS_ONE, Person::class, 'id_company' ],
    //'BILLING_ACCOUNTS' => [ self::HAS_MANY, BillingAccount::class, 'id_company', ],
    'ACTIVITIES' => [ self::HAS_MANY, CompanyActivity::class, 'id_company', 'id' ],
    'DOCUMENTS' => [ self::HAS_MANY, CompanyDocument::class, 'id_company', 'id'],
    'TAGS' => [ self::HAS_MANY, CompanyTag::class, 'id_company', 'id' ],
    'LEADS' => [ self::HAS_MANY, Lead::class, 'id_company', 'id'],
    'DEALS' => [ self::HAS_MANY, Deal::class, 'id_company', 'id'],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'name' => (new Varchar($this, $this->translate("Name")))->setRequired(),
      'street_line_1' => (new Varchar($this, $this->translate("Street Line 1"))),
      'street_line_2' => (new Varchar($this, $this->translate("Street Line 2"))),
      'region' => (new Varchar($this, $this->translate("Region"))),
      'city' => (new Varchar($this, $this->translate("City"))),
      'postal_code' => (new Varchar($this, $this->translate("Postal Code"))),
      'id_country' => (new Lookup($this, $this->translate("Country"), Country::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL'),
      'vat_id' => (new Varchar($this, $this->translate("VAT ID"))),
      'company_id' => (new Varchar($this, $this->translate("Company ID"))),
      'tax_id' => (new Varchar($this, $this->translate("Tax ID"))),
      'note' => (new Text($this, $this->translate("Notes"))),
      'date_created' => (new Date($this, $this->translate("Date Created")))->setReadonly()->setRequired(),
      'is_active' => (new Boolean($this, $this->translate("Active")))->setDefaultValue(1),
      'id_user' => (new Lookup($this, $this->translate("Assigned User"), User::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL')->setRequired()->setDefaultValue(1),
    ]));
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
      "company_id" => [
        "type" => "unique",
        "columns" => [
          "company_id" => [
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

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = $this->translate('Companies');
    $description['ui']['addButtonText'] = $this->translate('Add Company');
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    $description['columns']['tags'] = ["title" => "Tags"];

    unset($description['columns']['street_line_1']);
    unset($description['columns']['street_line_2']);
    unset($description['columns']['city']);
    unset($description['columns']['postal_code']);
    unset($description['columns']['region']);
    unset($description['columns']['id_country']);
    unset($description['columns']['note']);

    //nadstavit aby bol is_active posledný
    $tempColumn = $description['columns']['is_active'];
    unset($description['columns']['is_active']);
    $description['columns']['is_active'] = $tempColumn;


    return $description;
  }

  public function formDescribe(array $description = []): array
  {
    $description = parent::formDescribe();
    $description['defaultValues']['is_active'] = 0;
    $description['defaultValues']['id_user'] = $this->main->auth->getUserId();
    $description['defaultValues']['date_created'] = date("Y-m-d");
    $description['includeRelations'] = [
      'PERSONS',
      'COUNTRY',
      'FIRST_CONTACT',
      'BILLING_ACCOUNTS',
      'ACTIVITIES',
      'TAGS',
      'LEADS',
      'DEALS',
      'USER',
      'DOCUMENTS',
    ];
    $description['permissions']['canRead'] = $this->main->permissions->granted($this->fullName . ':Read');
    $description['permissions']['canCreate'] = $this->main->permissions->granted($this->fullName . ':Create');
    $description['permissions']['canUpdate'] = $this->main->permissions->granted($this->fullName . ':Update');
    $description['permissions']['canDelete'] = $this->main->permissions->granted($this->fullName . ':Delete');
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
