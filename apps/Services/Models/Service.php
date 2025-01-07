<?php

namespace HubletoApp\Services\Models;

use HubletoApp\Settings\Models\Currency;

class Service extends \HubletoCore\Core\Model
{
  public string $table = 'services';
  public string $eloquentClass = Eloquent\Service::class;
  public ?string $lookupSqlValue = "{%TABLE%}.name";

  public array $relations = [
    'CURRENCY' => [ self::HAS_ONE, Currency::class, 'id', 'id_currency'],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
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
        'model' => 'HubletoApp/Settings/Models/Currency',
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
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = 'Services';
    $description['ui']['addButtonText'] = 'Add Service';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    unset($description['columns']['description']);
    return $description;
  }

}
