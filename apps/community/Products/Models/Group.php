<?php

namespace HubletoApp\Community\Products\Models;

class Group extends \HubletoMain\Core\Model
{
  public string $table = 'product_groups';
  public string $eloquentClass = Eloquent\Group::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';

  public function columnsLegacy(array $columns = []): array
  {
    return parent::columnsLegacy(array_merge($columns,[

      "title" => [
        "type" => "varchar",
        "title" => $this->translate("Title"),
        "required" => true,
      ],

    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe();

    if (is_array($description['ui'])) {
      $description['ui']['title'] = 'Product Groups';
      $description["ui"]["addButtonText"] = $this->translate("Add product group");
    }

    return $description;
  }
}
