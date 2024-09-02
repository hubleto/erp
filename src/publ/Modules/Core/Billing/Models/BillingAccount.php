<?php

namespace CeremonyCrmApp\Modules\Core\Billing\Models;

class BillingAccount extends \CeremonyCrmApp\Core\Model
{
  public string $fullTableSqlName = 'billing_accounts';
  public string $table = 'billing_accounts';
  public string $eloquentClass = Eloquent\BillingAccount::class;
  public ?string $lookupSqlValue = "{%TABLE%}.description";


  public array $relations = [
    'SERVICES' => [ self::HAS_MANY, BillingAccountService::class, "id_billing_account", "id" ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      "id_company" => [
        "type" => "lookup",
        "title" => "Company",
        "model" => "CeremonyCrmApp/Modules/Core/Customers/Models/Company",
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
      ],
      "description" => [
        "type" => "varchar",
        "title" => "Description"
      ]
    ]));
  }

  public function tableParams(array $params = []): array
  {
    $params = parent::tableParams();
    $params['title'] = 'Billing Account';
    return $params;
  }

}
