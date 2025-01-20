<?php

namespace HubletoApp\Community\Shop\Models;

class ProductGroup extends \HubletoMain\Core\Model
{
  public string $table = 'product_groups';
  public string $eloquentClass = Eloquent\ProductGroup::class;
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

    $description['ui']['title'] = 'Product Groups';
    $description["ui"]["addButtonText"] = $this->translate("Add product group");

    return $description;
  }
}
