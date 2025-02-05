<?php

namespace HubletoApp\Community\Orders\Models;

class History extends \HubletoMain\Core\Model
{
  public string $table = 'order_histories';
  public string $eloquentClass = Eloquent\History::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id';

  public array $relations = [
    'ORDER' => [ self::BELONGS_TO, Order::class, 'id_order', 'id'],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      "id_order" => (new \ADIOS\Core\Db\Column\Lookup($this, $this->translate("Order"), Order::class))->setRequired()->setReadonly(),
      "short_description" => (new \ADIOS\Core\Db\Column\Varchar($this, $this->translate("Short Description")))->setReadonly(),
      "long_description" => (new \ADIOS\Core\Db\Column\Text($this, $this->translate("Long Description")))->setReadonly(),
      "date_time" => (new \ADIOS\Core\Db\Column\DateTime($this, $this->translate("Date Time")))->setRequired()->setReadonly(),
    ]));
  }
}
