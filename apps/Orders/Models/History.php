<?php

namespace HubletoApp\Community\Orders\Models;

class History extends \Hubleto\Framework\Models\Model
{
  public string $table = 'order_histories';
  public string $recordManagerClass = RecordManagers\History::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id';

  public array $relations = [
    'ORDER' => [ self::BELONGS_TO, Order::class, 'id_order', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_order' => (new \Hubleto\Framework\Db\Column\Lookup($this, $this->translate("Order"), Order::class))->setRequired()->setReadonly(),
      'short_description' => (new \Hubleto\Framework\Db\Column\Varchar($this, $this->translate("Short Description")))->setReadonly(),
      'long_description' => (new \Hubleto\Framework\Db\Column\Text($this, $this->translate("Long Description")))->setReadonly(),
      'date_time' => (new \Hubleto\Framework\Db\Column\DateTime($this, $this->translate("Date Time")))->setRequired()->setReadonly(),
    ]);
  }
}
