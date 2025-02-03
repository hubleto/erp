<?php

namespace HubletoApp\Community\Leads\Models;

use HubletoApp\Community\Services\Models\Service;
use HubletoApp\Community\Leads\Models\Lead;

use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Integer;
use \ADIOS\Core\Db\Column\Decimal;

class LeadService extends \HubletoMain\Core\Model
{
  public string $table = 'lead_services';
  public string $eloquentClass = Eloquent\LeadService::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id_service';

  public array $relations = [
    'SERVICE' => [ self::BELONGS_TO, Service::class, 'id_service', 'id' ],
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_lead', 'id' ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'id_lead' => (new Lookup($this, $this->translate('Lead'), Lead::class))->setRequired(),
      'id_service' => (new Lookup($this, $this->translate('Service'), Service::class))->setRequired(),
      'unit_price' => (new Decimal($this, $this->translate('Unit Price')))->setRequired(),
      'amount' => (new Integer($this, $this->translate('Amount')))->setRequired(),
      'discount' => new Decimal($this, $this->translate('Dicount (%)')),
      'tax' => new Decimal($this, $this->translate('Tax (%)')),
    ]));
  }

  public function tableDescribe(): \ADIOS\Core\Description\Table
  {
    $description = parent::tableDescribe();
    if ($this->main->urlParamAsInteger('idLead') > 0){
      $description->permissions = [
        'canRead' => $this->main->permissions->granted($this->fullName . ':Read'),
        'canCreate' => $this->main->permissions->granted($this->fullName . ':Create'),
        'canUpdate' => $this->main->permissions->granted($this->fullName . ':Update'),
        'canDelete' => $this->main->permissions->granted($this->fullName . ':Delete'),
      ];
    }

    return $description;
  }
}
