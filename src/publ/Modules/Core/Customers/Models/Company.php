<?php

namespace CeremonyCrmApp\Modules\Core\Customers\Models;

class Company extends \ADIOS\Core\Model
{
  public string $fullTableSqlName = 'companies';
  public string $table = 'companies';
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

  public function tableParams(array $params = []): array {
    $params = parent::tableParams();
    $params['title'] = 'Companies';
    $params['addButtonText'] = 'Add company';
    return $params;
  }

}
