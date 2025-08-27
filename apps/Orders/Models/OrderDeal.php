<?php

namespace Hubleto\App\Community\Orders\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\App\Community\Deals\Models\Deal;

class OrderDeal extends \Hubleto\Erp\Model
{
  public string $table = 'orders_deals';
  public string $recordManagerClass = RecordManagers\OrderDeal::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id';

  public array $relations = [
    'ORDER' => [ self::BELONGS_TO, Order::class, 'id_order', 'id'],
    'DEAL' => [ self::BELONGS_TO, Deal::class, 'id_deal', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_order' => (new Lookup($this, $this->translate('Order'), Order::class))->setRequired(),
      'id_deal' => (new Lookup($this, $this->translate('Deal'), Deal::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Order Deals';
    $description->ui["addButtonText"] = $this->translate("Add deal");

    if ($this->getRouter()->urlParamAsInteger('idOrder') > 0) {
      $description->columns = [];
      $description->inputs = [];
      $description->ui = [];
    }

    return $description;
  }
}
