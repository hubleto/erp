<?php

namespace Hubleto\App\Community\Products\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Integer;

class Unit extends \Hubleto\Erp\Model
{
  public string $table = 'product_units';
  public string $recordManagerClass = RecordManagers\Unit::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';
  public ?string $lookupUrlDetail = 'products/units/{%ID%}';
  public ?string $lookupUrlAdd = 'products/units/add';

  const CATEGORY_BASE = 1;
  const CATEGORY_CONTAINER = 2;

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      // short label, e.g. "kg", "pcs", "carton" - this is what lookups display
      'name' => (new Varchar($this, $this->translate("Name")))->setRequired()->setDefaultVisible()->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      'category' => (new Integer($this, $this->translate("Category")))->setRequired()->setDefaultValue(self::CATEGORY_BASE)->setEnumValues([
        self::CATEGORY_BASE => $this->translate("Base unit"),
        self::CATEGORY_CONTAINER => $this->translate("Packaging unit"),
      ])->setDefaultVisible(),
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
