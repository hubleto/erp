<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

class BusinessAccount extends \CeremonyCrmApp\Core\Model
{
  public string $fullTableSqlName = 'business_accounts';
  public string $table = 'business_accounts';
  public string $eloquentClass = Eloquent\BusinessAccount::class;
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
    $params['title'] = 'Business Account';
    return $params;
  }

}
