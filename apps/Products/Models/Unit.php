<?php

namespace Hubleto\App\Community\Products\Models;

use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Varchar;

class Unit extends \Hubleto\Erp\Model
{
  public string $table = 'product_units';
  public string $recordManagerClass = RecordManagers\Unit::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';
  public ?string $lookupUrlDetail = 'products/units/{%ID%}';
  public ?string $lookupUrlAdd = 'products/units/add';

  // second line in a lookup: which pallet you are picking is mostly a question of what it weighs
  public function getLookupDetails(array $dataRaw): string
  {
    $tareWeight = (float) ($dataRaw['tare_weight'] ?? 0);
    return $tareWeight > 0 ? $tareWeight . ' kg ' . $this->translate('tare') : '';
  }

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate("Name")))->setRequired()->setDefaultVisible()->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      // shared default, so a euro pallet's 25 kg is entered once instead of per product
      'tare_weight' => (new Decimal($this, $this->translate('Standard tare weight')))->setUnit('kg')->setDefaultVisible()
        ->setHint($this->translate('Weight of this container when empty. A product can override it on its own packaging level.')),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui["addButtonText"] = $this->translate("Add packaging unit");
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }
}
