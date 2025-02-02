<?php

namespace HubletoApp\Community\Services\Models;

use HubletoApp\Community\Settings\Models\Currency;

use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Decimal;
use \ADIOS\Core\Db\Column\Lookup;

class Service extends \HubletoMain\Core\Model
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
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired(),
      'price' => (new Decimal($this, $this->translate('Unit price'))),
      'id_currency' => (new Lookup($this, $this->translate('Currency'), Currency::class))->setRequired()->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL'),
      'unit' => (new Varchar($this, $this->translate('Unit'))),
      'description' => (new Varchar($this, $this->translate('Description'))),
    ]));
  }

  public function tableDescribe(): \ADIOS\Core\Description\Table
  {
    $description = parent::tableDescribe();

    $description->ui['title'] = 'Services';
    $description->ui['addButtonText'] = 'Add Service';
    $description->ui['showHeader'] = true;
    $description->ui['showFooter'] = false;

    unset($description->columns['description']);

    return $description;
  }

}
