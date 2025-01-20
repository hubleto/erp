<?php

namespace HubletoApp\Community\Products\Models;

class Supplier extends \HubletoMain\Core\Model
{
  public string $table = 'product_suppliers';
  public string $eloquentClass = Eloquent\Supplier::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns,[

      "title" => [
        "type" => "varchar",
        "title" => $this->translate("Title"),
        "required" => true,
      ],

    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe();

    $description['ui']['title'] = 'Product Suppliers';
    $description["ui"]["addButtonText"] = $this->translate("Add product supplier");

    return $description;
  }
}
