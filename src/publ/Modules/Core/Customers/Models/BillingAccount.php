<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

class BillingAccount extends \CeremonyCrmApp\Core\Model
{
  public string $fullTableSqlName = 'billing_accounts';
  public string $table = 'billing_accounts';
  public string $eloquentClass = Eloquent\BillingAccount::class;
  public ?string $lookupSqlValue = "{%TABLE%}.id";

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
      "name" => [
        "type" => "varchar",
        "title" => "Name"
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
