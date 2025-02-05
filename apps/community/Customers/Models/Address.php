<?php

namespace HubletoApp\Community\Customers\Models;

use HubletoApp\Community\Settings\Models\Country;

use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Varchar;

class Address extends \HubletoMain\Core\Model
{
  public string $table = 'addresses';
  public string $eloquentClass = Eloquent\Address::class;
  public ?string $lookupSqlValue = "concat({%TABLE%}.street_line_1, ', ', {%TABLE%}.street_line_2, ', ', {%TABLE%}.city)";

  public array $relations = [
    'PERSON' => [ self::BELONGS_TO, Person::class, 'id_person', 'id' ],
    'COUNTRY' => [ self::HAS_ONE, Country::class, 'id', 'id_country' ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'id_person' => (new Lookup($this, $this->translate("Person"), Person::class, 'CASCADE'))->setRequired(),
      'street_line_1' => (new Varchar($this, $this->translate("Street Line 1")))->setRequired(),
      'street_line_2' => (new Varchar($this, $this->translate("Street Line 2"))),
      'region' => (new Varchar($this, $this->translate("Region")))->setRequired(),
      'city' => (new Varchar($this, $this->translate("City")))->setRequired(),
      'postal_code' => (new Varchar($this, $this->translate("Postal Code")))->setRequired(),
      'id_country' => (new Lookup($this, $this->translate("Country"), Country::class, 'SET NULL'))->setRequired(),
    ]));
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Contacts';
    $description->ui['addButtonText'] = 'Add Contact';
    $description->ui['showHeader'] = true;
    $description->ui['showFooter'] = false;
    return $description;
  }

}
