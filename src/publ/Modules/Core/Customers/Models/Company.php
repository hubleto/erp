<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

class Company extends \ADIOS\Core\Model
{
  public string $fullTableSqlName = 'customers';
  public string $table = 'customers';
  public string $eloquentClass = Eloquent\Company::class;

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      "name" => [
        "type" => "varchar",
        "title" => "Name",
      ],
    ]));
  }

}
