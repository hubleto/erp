<?php

namespace HubletoApp\Community\Customers\Models;

use HubletoApp\Community\Customers\Models\Tag;

use \ADIOS\Core\Db\Column\Lookup;

class CustomerTag extends \HubletoMain\Core\Model
{
  public string $table = 'cross_customer_tags';
  public string $recordManagerClass = RecordManagers\CustomerTag::class;

  public array $relations = [
    'TAG' => [ self::BELONGS_TO, Tag::class, 'id_tag', 'id' ],
    'CUSTOMER' => [ self::BELONGS_TO, Customer::class, 'id_customer', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class, 'CASCADE'))->setRequired(),
      'id_tag' => (new Lookup($this, $this->translate('Tag'), Tag::class, 'CASCADE'))->setRequired(),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Customer Categories';
    $description->ui['addButtonText'] = 'Add Customer';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;
    return $description;
  }

}
