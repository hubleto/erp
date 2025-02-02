<?php

namespace HubletoApp\Community\Products\Models;

class Group extends \HubletoMain\Core\Model
{
  public string $table = 'product_groups';
  public string $eloquentClass = Eloquent\Group::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      "title" => (new \ADIOS\Core\Db\Column\Varchar($this, $this->translate("Title")))->setRequired()
    ]));
  }

  public function tableDescribe(): \ADIOS\Core\Description\Table
  {
    $description = parent::tableDescribe();

    $description->ui['title'] = 'Product Groups';
    $description->ui["addButtonText"] = $this->translate("Add product group");

    return $description;
  }
}
