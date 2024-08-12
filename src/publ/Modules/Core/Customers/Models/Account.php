<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

class Account extends \CeremonyCrmApp\Core\Model
{
  public string $fullTableSqlName = 'accounts';
  public string $table = 'accounts';
  public string $eloquentClass = Eloquent\Account::class;
  public ?string $lookupSqlValue = "{%TABLE%}.name";

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      "name" => [
        "type" => "varchar",
        "title" => "Name",
      ],
      /* "tags" => [
        "type" => "tags",
        "title" => "Tags",
      ], */
    ]));
  }

  public function tableParams(array $params = []): array
  {
    $params = parent::tableParams();
    $params['title'] = 'Account';
    return $params;
  }

}
