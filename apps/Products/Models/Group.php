<?php

namespace HubletoApp\Community\Products\Models;

class Group extends \HubletoMain\Model
{
  public string $table = 'product_groups';
  public string $recordManagerClass = RecordManagers\Group::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';
  public ?string $lookupUrlDetail = 'products/groups/{%ID%}';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      "title" => (new \Hubleto\Framework\Db\Column\Varchar($this, $this->translate("Title")))->setRequired()
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Product Groups';
    $description->ui["addButtonText"] = $this->translate("Add product group");

    return $description;
  }
}
