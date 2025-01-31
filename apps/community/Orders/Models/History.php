<?php

namespace HubletoApp\Community\Orders\Models;

class History extends \HubletoMain\Core\Model
{
  public string $table = 'order_histories';
  public string $eloquentClass = Eloquent\History::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id';

  public array $relations = [
    'ORDER'   => [ self::BELONGS_TO, Order::class, 'id_order', 'id'],
  ];

  public function columnsLegacy(array $columns = []): array
  {
    return parent::columnsLegacy(array_merge($columns,[
      "id_order" => [
        "type" => "lookup",
        "model" => Order::class,
        "title" => $this->translate("Order"),
        "required" => true,
        "readonly" => true,
      ],

      "short_description" => [
        "type" => "varchar",
        "title" =>  $this->translate("Short Description"),
        "required" => false,
        "readonly" => true,
      ],

      "long_description" => [
        "type" => "text",
        "title" =>  $this->translate("Long Description"),
        "required" => false,
        "readonly" => true,
      ],

      "date_time" => [
        "type" => "datetime",
        "title" => $this->translate("Date Time"),
        "required" => true
      ],

    ]));
  }
}
