<?php

namespace HubletoApp\Community\Services\Models;

use HubletoApp\Community\Settings\Models\Currency;

class Service extends \HubletoMain\Core\Model
{
  public string $table = 'services';
  public string $eloquentClass = Eloquent\Service::class;
  public ?string $lookupSqlValue = "{%TABLE%}.name";

  public array $relations = [
    'CURRENCY' => [ self::HAS_ONE, Currency::class, 'id', 'id_currency'],
  ];

  public function columnsLegacy(array $columns = []): array
  {
    return parent::columnsLegacy(array_merge($columns, [
      "name" => [
        "type" => "varchar",
        "title" => "Name",
        "required" => true,
      ],
      "price" => [
        "type" => "float",
        "title" => "Unit Price",
      ],
      'id_currency' => [
        'type' => 'lookup',
        'title' => 'Currency',
        'model' => 'HubletoApp/Community/Settings/Models/Currency',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'SET NULL',
      ],
      "unit" => [
        "type" => "varchar",
        "title" => "Unit",
      ],
      "description" => [
        "type" => "varchar",
        "title" => "Description"
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe($description);

    if (is_array($description['ui'])) {
      $description['ui']['title'] = 'Services';
      $description['ui']['addButtonText'] = 'Add Service';
      $description['ui']['showHeader'] = true;
      $description['ui']['showFooter'] = false;
    }

    if (is_array($description['columns'])) {
      unset($description['columns']['description']);
    }

    return $description;
  }

}
