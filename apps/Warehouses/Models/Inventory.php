<?php

namespace Hubleto\App\Community\Warehouses\Models;


use Hubleto\App\Community\Warehouses\Models\Location;
use Hubleto\App\Community\Products\Models\Product;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\DateTime;

// This is a crucial table that links products to their specific locations and quantities.
// This is crucial for tracking what items are where.
class Inventory extends \Hubleto\Erp\Model
{
  public string $table = 'warehouses_inventory';
  public string $recordManagerClass = RecordManagers\Inventory::class;

  public array $relations = [
    'PRODUCT' => [ self::HAS_ONE, Product::class, 'id_product', 'id' ],
    'LOCATION' => [ self::HAS_ONE, Location::class, 'id_location', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_product' => (new Lookup($this, $this->translate('Product'), Product::class))->setDefaultVisible()->setRequired()->addIndex('UNIQUE `id_product__id_location` (`id_product`, `id_location`)'),
      'id_location' => (new Lookup($this, $this->translate('Location in warehouse'), Location::class))->setDefaultVisible()->setRequired(),
      'quantity' => (new Decimal($this, $this->translate('Quantity')))->setDefaultVisible(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add item';
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton', 'footer']);
    return $description;
  }

}
