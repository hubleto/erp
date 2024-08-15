<?php

namespace CeremonyCrmApp\Modules\Core\Sandbox\Models;

class CompanyCategory extends \CeremonyCrmApp\Core\Model
{
  public string $fullTableSqlName = 'companies_categories';
  public string $table = 'companies_categories';
  public string $eloquentClass = Eloquent\CompanyCategory::class;

  public array $relations = [
    'CATEGORY' => [ self::BELONGS_TO, Category::class, 'id_category' ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      "id_company" => [
        "type" => "lookup",
        "title" => "Company",
        "model" => "CeremonyCrmApp/Modules/Core/Sandbox/Models/Company",
      ],
      "id_category" => [
        "type" => "lookup",
        "title" => "Category",
        "model" => "CeremonyCrmApp/Modules/Core/Sandbox/Models/Category",
      ],
    ]));
  }

  public function tableParams(array $params = []): array
  {
    $params = parent::tableParams();
    $params['title'] = 'Companies - Categories';
    return $params;
  }

}
