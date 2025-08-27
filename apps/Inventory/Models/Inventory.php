<?php

namespace HubletoApp\Community\Inventory\Models;

use HubletoApp\Community\Settings\Models\User;
use HubletoApp\Community\Warehouses\Models\Location;
use HubletoApp\Community\Products\Models\Product;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\DateTime;

// This is a crucial table that links products to their specific locations and quantities.
// This is crucial for tracking what items are where.
class Inventory extends \HubletoMain\Model
{
  public string $table = 'inventory';
  public string $recordManagerClass = RecordManagers\Inventory::class;

  public array $relations = [
    'PRODUCT' => [ self::HAS_ONE, Product::class, 'id_product', 'id' ],
    'STATUS' => [ self::HAS_ONE, Status::class, 'id_status', 'id' ],
    'LOCATION' => [ self::HAS_ONE, Location::class, 'id_location', 'id' ],
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_product' => (new Lookup($this, $this->translate('Product'), Product::class))->setProperty('defaultVisibility', true)->setRequired(),
      'id_location' => (new Lookup($this, $this->translate('Location in warehouse'), Location::class))->setProperty('defaultVisibility', true)->setRequired(),
      'id_status' => (new Lookup($this, $this->translate('Status'), Status::class))->setProperty('defaultVisibility', true),
      'quantity' => (new Decimal($this, $this->translate('Quantity')))->setProperty('defaultVisibility', true),
      'batch_number' => (new Varchar($this, $this->translate('Batch number'))),
      'serial_number' => (new Varchar($this, $this->translate('Serial number'))),
      'datetime_expiration' => (new DateTime($this, $this->translate('Expiration'))),
      'datetime_received' => (new DateTime($this, $this->translate('Received'))),
      'datetime_last_move' => (new DateTime($this, $this->translate('Last moved'))),
    ]);
  }

}
