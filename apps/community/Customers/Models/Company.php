<?php

namespace HubletoApp\Community\Customers\Models;

use HubletoApp\Community\Billing\Models\BillingAccount;
use HubletoApp\Community\Settings\Models\Country;
use HubletoApp\Community\Settings\Models\User;
use HubletoApp\Community\Deals\Models\Deal;
use HubletoApp\Community\Leads\Models\Lead;
use Illuminate\Database\Eloquent\Builder;

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
      'name' => [
        'type' => 'varchar',
        'title' => $this->translate('Name'),
        'required' => true,
      ],
      'street_line_1' => [
        'type' => 'varchar',
        'title' => $this->translate('Street Line 1'),
        'required' => false,
      ],
      'street_line_2' => [
        'type' => 'varchar',
        'title' => $this->translate('Street Line 2'),
        'required' => false,
      ],
      'region' => [
        'type' => 'varchar',
        'title' => $this->translate('Region'),
        'required' => false,
      ],
      'city' => [
        'type' => 'varchar',
        'title' => $this->translate('City'),
        'required' => false,
      ],
      'id_country' => [
        'type' => 'lookup',
        'model' => \HubletoApp\Community\Settings\Models\Country::class,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'SET NULL',
        'title' => $this->translate('Country'),
        'required' => false,
      ],
      'postal_code' => [
        'type' => 'varchar',
        'title' => $this->translate('Postal Code'),
        'required' => false,
      ],
      'vat_id' => [
        'type' => 'varchar',
        'title' => $this->translate('VAT ID'),
        'required' => false,
      ],
      'company_id' => [
        'type' => 'varchar',
        'title' => $this->translate('Company ID'),
        'required' => false,
      ],
      'tax_id' => [
        'type' => 'varchar',
        'title' => $this->translate('Tax ID'),
        'required' => true,
      ],
      'note' => [
        'type' => 'text',
        'title' => $this->translate('Notes'),
        'required' => false,
      ],
      'date_created' => [
        'type' => 'date',
        'title' => $this->translate('Date Created'),
        'required' => true,
        'readonly' => true,
      ],
      'is_active' => [
        'type' => 'boolean',
        'title' => $this->translate('Active'),
        'required' => false,
        'default' => 1,
      ],
      'id_user' => [
        'type' => 'lookup',
        'title' => $this->translate('Assigned User'),
        'model' => \HubletoApp\Community\Settings\Models\User::class,
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'SET NULL',
        'required' => true,
        //'readonly' => $this->main->permissions->granted($this->fullName . ':Update'),
        'default' => 1,
      ]

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
    $description = parent::formDescribe($description);
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

  public function prepareLoadRecordQuery(array|null $includeRelations = null, int $maxRelationLevel = 0, mixed $query = null, int $level = 0): mixed {
    $query = parent::prepareLoadRecordQuery($includeRelations, 3);
    return $query;
  }

}
