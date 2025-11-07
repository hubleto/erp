<?php

namespace Hubleto\App\Community\Products\Models;

use Hubleto\Framework\Db\Column\Varchar;

class Group extends \Hubleto\Erp\Model
{
  public string $table = 'product_groups';
  public string $recordManagerClass = RecordManagers\Group::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';
  public ?string $lookupUrlDetail = 'products/groups/{%ID%}';
  public ?string $lookupUrlAdd = 'products/groups/add';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate("Name")))->setDefaultVisible()->setIcon(self::COLUMN_NAME_DEFAULT_ICON)
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui["addButtonText"] = $this->translate("Add product group");
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }
}
