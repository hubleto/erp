<?php

namespace HubletoApp\Community\Services\Models;

use HubletoApp\Community\Settings\Models\Currency;

use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Decimal;
use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Text;

class Service extends \HubletoMain\Core\Model
{
  public string $table = 'services';
  public string $recordManagerClass = RecordManagers\Service::class;
  public ?string $lookupSqlValue = "{%TABLE%}.name";

  public array $relations = [
    'CURRENCY' => [ self::HAS_ONE, Currency::class, 'id', 'id_currency'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired(),
      'price' => (new Decimal($this, $this->translate('Unit price'))),
      'id_currency' => (new Lookup($this, $this->translate('Currency'), Currency::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL')->setRequired(),
      'unit' => (new Varchar($this, $this->translate('Unit'))),
      'description' => (new Text($this, $this->translate('Description'))),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Services';
    $description->ui['addButtonText'] = 'Add Service';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    unset($description->columns['description']);

    return $description;
  }

}
