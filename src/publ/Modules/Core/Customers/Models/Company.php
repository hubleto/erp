<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

use CeremonyCrmApp\Modules\Core\Billing\Models\BillingAccount;
use CeremonyCrmApp\Modules\Core\Settings\Models\Country;
use CeremonyCrmApp\Modules\Core\Settings\Models\User;
use CeremonyCrmApp\Modules\Sales\Sales\Models\Deal;
use CeremonyCrmApp\Modules\Sales\Sales\Models\Lead;
use Illuminate\Database\Eloquent\Builder;

class Company extends \CeremonyCrmApp\Core\Model
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
        'title' => 'Name',
        'required' => true,
      ],
      'street_line_1' => [
        'type' => 'varchar',
        'title' => 'Street Line 1',
        'required' => false,
      ],
      'street_line_2' => [
        'type' => 'varchar',
        'title' => 'Street Line 2',
        'required' => false,
      ],
      'region' => [
        'type' => 'varchar',
        'title' => 'Region',
        'required' => false,
      ],
      'city' => [
        'type' => 'varchar',
        'title' => 'City',
        'required' => false,
      ],
      'id_country' => [
        'type' => 'lookup',
        'model' => 'CeremonyCrmApp/Modules/Core/Settings/Models/Country',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'SET NULL',
        'title' => 'Country',
        'required' => false,
      ],
      'postal_code' => [
        'type' => 'varchar',
        'title' => 'Postal Code',
        'required' => false,
      ],
      'vat_id' => [
        'type' => 'varchar',
        'title' => 'VAT ID',
        'required' => false,
      ],
      'company_id' => [
        'type' => 'varchar',
        'title' => 'Company ID',
        'required' => false,
      ],
      'tax_id' => [
        'type' => 'varchar',
        'title' => 'Tax ID',
        'required' => true,
      ],
      'note' => [
        'type' => 'text',
        'title' => 'Notes',
        'required' => false,
      ],
      'date_created' => [
        'type' => 'date',
        'title' => 'Date Created',
        'required' => true,
        'readonly' => true,
      ],
      'is_active' => [
        'type' => 'boolean',
        'title' => 'Active',
        'required' => false,
        'default' => 1,
      ],
      'id_user' => [
        'type' => 'lookup',
        'title' => 'Assigned User',
        'model' => 'CeremonyCrmApp/Modules/Core/Settings/Models/User',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'SET NULL',
        'required' => true,
        //'readonly' => $this->app->permissions->granted($this->fullName . ':Update'),
        'default' => 1,
      ]

    ]));
  }

  public function indexes(array $indexes = []) {
    return parent::indexes([
      "vat_id" => [
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
      ],
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
    $description['ui']['title'] = 'Companies';
    $description['ui']['addButtonText'] = 'Add Company';
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
    $description['defaultValues']['is_active'] = 1;
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
    $description['permissions']['canRead'] = $this->app->permissions->granted($this->fullName . ':Read');
    $description['permissions']['canCreate'] = $this->app->permissions->granted($this->fullName . ':Create');
    $description['permissions']['canUpdate'] = $this->app->permissions->granted($this->fullName . ':Update');
    $description['permissions']['canDelete'] = $this->app->permissions->granted($this->fullName . ':Delete');
    return $description;
  }

  public function getNewRecordDataFromString(string $text): array {
    return [
      'name' => $text,
    ];
  }

  public function prepareLoadRecordQuery(array|null $includeRelations = null, int $maxRelationLevel = 0, $query = null, int $level = 0)
  {
    $query = parent::prepareLoadRecordQuery($includeRelations, 3);
    return $query;
  }

}
