<?php

namespace Hubleto\App\Community\Products\Models;

use Hubleto\Framework\Db\Column\Varchar;

class Unit extends \Hubleto\Erp\Model
{
  public string $table = 'product_units';
  public string $recordManagerClass = RecordManagers\Unit::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';
  public ?string $lookupUrlDetail = 'products/units/{%ID%}';
  public ?string $lookupUrlAdd = 'products/units/add';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate("Name")))->setRequired()->setDefaultVisible()->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui["addButtonText"] = $this->translate("Add unit");
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }
}
