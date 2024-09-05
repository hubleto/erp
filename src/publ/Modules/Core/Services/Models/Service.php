<?php

namespace CeremonyCrmApp\Modules\Core\Services\Models;

class Service extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'services';
  public string $eloquentClass = Eloquent\Service::class;
  public ?string $lookupSqlValue = "{%TABLE%}.name";

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      "name" => [
        "type" => "varchar",
        "title" => "Name"
      ]
    ]));
  }

  public function tableParams(array $params = []): array
  {
    $params = parent::tableParams();
    $params['title'] = 'Services';
    return $params;
  }

}
