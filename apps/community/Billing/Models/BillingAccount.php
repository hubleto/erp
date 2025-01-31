<?php

namespace HubletoApp\Community\Billing\Models;

use HubletoApp\Community\Customers\Models\Company;

class BillingAccount extends \HubletoMain\Core\Model
{
  public string $table = 'billing_accounts';
  public string $eloquentClass = Eloquent\BillingAccount::class;
  public ?string $lookupSqlValue = '{%TABLE%}.description';

  public array $relations = [
    'SERVICES' => [ self::HAS_MANY, BillingAccountService::class, 'id_billing_account', 'id' ],
    'COMPANY' => [ self::BELONGS_TO, Company::class, 'id_company', 'id'  ],
  ];

  public function columnsLegacy(array $columns = []): array
  {
    return parent::columnsLegacy(array_merge($columns, [
      'id_company' => [
        'type' => 'lookup',
        'title' => 'Company',
        'model' => 'HubletoApp/Community/Customers/Models/Company',
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
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = 'Billing Account';
    $description['ui']['addButtonText'] = 'Add Billing Account';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    return $description;
  }
}
