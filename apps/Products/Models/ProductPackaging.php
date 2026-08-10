<?php

namespace Hubleto\App\Community\Products\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Text;

class ProductPackaging extends \Hubleto\Erp\Model
{
  public string $table = 'product_packagings';
  public string $recordManagerClass = RecordManagers\ProductPackaging::class;

  public array $relations = [
    'PRODUCT' => [ self::BELONGS_TO, Product::class, 'id_product', 'id' ],
    'UNIT' => [ self::BELONGS_TO, Unit::class, 'id_unit', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_product' => (new Lookup($this, $this->translate('Product'), Product::class))->setRequired(),
      'id_unit' => (new Lookup($this, $this->translate('Packaging unit'), Unit::class))->setRequired()->setDefaultVisible(),
      'qty_per_lower' => (new Decimal($this, $this->translate('Quantity per lower level')))->setDefaultVisible(),
      'sort' => (new Integer($this, $this->translate('Order'))),
      'length' => (new Decimal($this, $this->translate('Length')))->setUnit('m'),
      'width' => (new Decimal($this, $this->translate('Width')))->setUnit('m'),
      'height' => (new Decimal($this, $this->translate('Height')))->setUnit('m'),
      'weight' => (new Decimal($this, $this->translate('Weight')))->setUnit('kg'),
      'description' => (new Text($this, $this->translate('Package description'))),
    ]);
  }
}
