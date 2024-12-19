<?php

namespace CeremonyCrmMod\Deals\Models;

use CeremonyCrmMod\Services\Models\Service;
use CeremonyCrmMod\Deals\Models\Deal;

class DealService extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'deal_services';
  public string $eloquentClass = Eloquent\DealService::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id_service';

  public array $relations = [
    'SERVICE' => [ self::BELONGS_TO, Service::class, 'id_service', 'id' ],
    'DEAL' => [ self::BELONGS_TO, Deal::class, 'id_deal', 'id' ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'id_deal' => [
        'type' => 'lookup',
        'title' => 'Deal',
        'model' => 'CeremonyCrmMod/Deals/Models/Deal',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
      'id_service' => [
        'type' => 'lookup',
        'title' => 'Service',
        'model' => 'CeremonyCrmMod/Services/Models/Service',
        'foreignKeyOnUpdate' => 'CASCADE',
        'foreignKeyOnDelete' => 'CASCADE',
        'required' => true,
      ],
      "unit_price" => [
        "type" => "float",
        "title" => "Unit Price",
        "required" => true,
      ],
      "amount" => [
        "type" => "int",
        "title" => "Amount",
        "required" => true,
      ],
      "discount" => [
        "type" => "float",
        "title" => "Dicount (%)",
      ],
      "tax" => [
        "type" => "float",
        "title" => "Tax (%)",
      ],
    ]));
  }
}
