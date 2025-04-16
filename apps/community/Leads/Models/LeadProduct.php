<?php

namespace HubletoApp\Community\Leads\Models;

use HubletoApp\Community\Leads\Models\Lead;
use HubletoApp\Community\Products\Models\Product;

use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Integer;
use \ADIOS\Core\Db\Column\Decimal;

class LeadProduct extends \HubletoMain\Core\Model
{
  public string $table = 'lead_products';
  public string $recordManagerClass = RecordManagers\LeadProduct::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id_product';

  public array $relations = [
    'PRODUCT' => [ self::BELONGS_TO, Product::class, 'id_product', 'id' ],
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_lead', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_lead' => (new Lookup($this, $this->translate('Lead'), Lead::class, 'CASCADE'))->setRequired(),
      'id_product' => (new Lookup($this, $this->translate('Product'), Product::class, 'CASCADE'))->setRequired(),
      'unit_price' => (new Decimal($this, $this->translate('Unit Price')))->setRequired(),
      'amount' => (new Integer($this, $this->translate('Amount')))->setRequired(),
      'discount' => new Decimal($this, $this->translate('Dicount (%)')),
      'tax' => new Decimal($this, $this->translate('Tax (%)')),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    if ($this->main->urlParamAsInteger('idLead') > 0){
      $description->permissions = [
        'canRead' => $this->main->permissions->granted($this->fullName . ':Read'),
        'canCreate' => $this->main->permissions->granted($this->fullName . ':Create'),
        'canUpdate' => $this->main->permissions->granted($this->fullName . ':Update'),
        'canDelete' => $this->main->permissions->granted($this->fullName . ':Delete'),
      ];
      $description->columns = [];
      $description->inputs = [];
      $description->ui = [];
    }

    return $description;
  }
}
