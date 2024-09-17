<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

use CeremonyCrmApp\Modules\Core\Billing\Models\BillingAccount;
use CeremonyCrmApp\Modules\Core\Settings\Models\Country;
use Illuminate\Database\Eloquent\Builder;

class Company extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'companies';
  public string $eloquentClass = Eloquent\Company::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public array $relations = [
    'PERSONS' => [ self::HAS_MANY, Person::class, 'id_company' ],
    'COUNTRY' => [ self::HAS_ONE, Country::class, 'id', 'id_country' ],
    'FIRST_CONTACT' => [ self::HAS_ONE, Person::class, 'id_company' ],
    'BILLING_ACCOUNT' => [ self::HAS_ONE, BillingAccount::class, 'id_company' ],
    'ACTIVITIES' => [ self::HAS_MANY, Activity::class, 'id_company' ],
    'TAGS' => [ self::HAS_MANY, CompanyTag::class, 'id_company', 'id' ],
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
        'required' => true,
      ],
      'street_line_2' => [
        'type' => 'varchar',
        'title' => 'Street Line 2',
      ],
      'region' => [
        'type' => 'varchar',
        'title' => 'Region',
        'required' => true,
      ],
      'city' => [
        'type' => 'varchar',
        'title' => 'City',
        'required' => true,
      ],
      'id_country' => [
        'type' => 'lookup',
        'model' => 'CeremonyCrmApp/Modules/Core/Settings/Models/Country',
        'foreignKeyOnUpdate' => 'SET NULL',
        'foreignKeyOnDelete' => 'SET NULL',
        'title' => 'Country',
        'required' => true,
      ],
      'postal_code' => [
        'type' => 'varchar',
        'title' => 'Postal Code',
        'required' => true,
      ],
      'vat_id' => [
        'type' => 'varchar',
        'title' => 'VAT ID',
      ],
      'company_id' => [
        'type' => 'varchar',
        'title' => 'Company ID',
      ],
      'tax_id' => [
        'type' => 'varchar',
        'title' => 'Tax ID',
      ],
      'note' => [
        'type' => 'text',
        'title' => 'Notes',
        'required' => false,
      ],
      'is_active' => [
        'type' => 'boolean',
        'title' => 'Active',
        'required' => false,
        'default' => 1,
        'required' => false,
      ],

    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe();
    $description['ui']['title'] = 'Companies';
    $description['ui']['addButtonText'] = 'Add Company';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    return $description;
  }

  public function formDescribe(array $description = []): array
  {
    $description = parent::formDescribe();
    $description['defaultValues']['is_active'] = 1;
    return $description;
  }

  public function getNewRecordDataFromString(string $text): array {
    return [
      'name' => $text,
    ];
  }

}
