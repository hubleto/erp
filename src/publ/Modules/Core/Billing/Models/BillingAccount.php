<?php

namespace CeremonyCrmApp\Modules\Core\Billing\Models;

use CeremonyCrmApp\Modules\Core\Customers\Models\Company;

class BillingAccount extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'billing_accounts';
  public string $eloquentClass = Eloquent\BillingAccount::class;
  public ?string $lookupSqlValue = "{%TABLE%}.description";

  public array $relations = [
    'SERVICES' => [ self::HAS_MANY, BillingAccountService::class, "id_billing_account", "id" ],
    'COMPANY' => [ self::BELONGS_TO, Company::class, "id" ],
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

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe();
    $description['title'] = 'Billing Account';
    return $description;
  }

}
