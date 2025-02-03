<?php

namespace HubletoApp\Community\Deals\Models;

use HubletoApp\Community\Services\Models\Service;
use HubletoApp\Community\Deals\Models\Deal;

use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Integer;
use \ADIOS\Core\Db\Column\Decimal;

class DealService extends \HubletoMain\Core\Model
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
      'id_deal' => (new Lookup($this, $this->translate('Deal'), Deal::class))->setRequired(),
      'id_service' => (new Lookup($this, $this->translate('Service'), Service::class))->setRequired(),
      'unit_price' => (new Decimal($this, $this->translate('Unit Price')))->setRequired(),
      'amount' => (new Integer($this, $this->translate('Amount')))->setRequired(),
      'discount' => new Decimal($this, $this->translate('Dicount (%)')),
      'tax' => new Decimal($this, $this->translate('Tax (%)')),
    ]));
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    if ($this->main->urlParamAsInteger('idDeal') > 0) {
      $description->permissions = [
        'canRead' => $this->main->permissions->granted($this->fullName . ':Read'),
        'canCreate' => $this->main->permissions->granted($this->fullName . ':Create'),
        'canUpdate' => $this->main->permissions->granted($this->fullName . ':Update'),
        'canDelete' => $this->main->permissions->granted($this->fullName . ':Delete'),
      ];
      $description->columns = [];
      $description->ui = [];
    }

    return $description;
  }
}
