<?php

namespace CeremonyCrmApp\Modules\Core\Billing\Models;

use CeremonyCrmApp\Modules\Core\Customers\Models\Company;

class BillingAccount extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'billing_accounts';
  public string $eloquentClass = Eloquent\BillingAccount::class;
  public ?string $lookupSqlValue = '{%TABLE%}.description';

  public array $relations = [
    'SERVICES' => [ self::HAS_MANY, BillingAccountService::class, 'id_billing_account', 'id' ],
    'COMPANY' => [ self::BELONGS_TO, Company::class, 'id_company', 'id'  ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'id_company' => [
        'type' => 'lookup',
        'title' => 'Company',
        'model' => 'CeremonyCrmApp/Modules/Core/Customers/Models/Company',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true
      ],
      'description' => [
        'type' => 'varchar',
        'title' => 'Description',
        'required' => true,
      ]
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe();
    $description['title'] = 'Billing Accounts';
    return $description;
  }

  /* public function formDescribe(array $description = []): array
  {
    $description = parent::formDescribe();
    $description['includeRelations'] = ['SERVICES', 'COMPANY'];
    return $description;
  } */

  /* public function prepareLoadRecordQuery(array|null $includeRelations = null, int $maxRelationLevel = 0, $query = null, int $level = 0)
  {
    $query = parent::prepareLoadRecordQuery(['SERVICES', 'COMPANY'],0);
    return $query;
  } */
}
