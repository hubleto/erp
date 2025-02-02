<?php

namespace HubletoApp\Community\Billing\Models;

use HubletoApp\Community\Services\Models\Service;

use \ADIOS\Core\Db\Column\Lookup;

class BillingAccountService extends \HubletoMain\Core\Model
{
  public string $table = 'billing_accounts_services';
  public string $eloquentClass = Eloquent\BillingAccountService::class;

  public array $relations = [
    'SERVICE' => [ self::BELONGS_TO, Service::class, 'id_service', 'id' ],
    'BILLING_ACCOUNT' => [ self::BELONGS_TO, BillingAccount::class, 'id_billing_account', 'id' ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'id_billing_account' => (new Lookup($this, $this->translate("Billing Account"), BillingAccount::class, 'CASCADE'))->setRequired(),
      'id_service' => (new Lookup($this, $this->translate("Service"), Service::class, 'CASCADE'))->setRequired(),
    ]));
  }

  public function tableDescribe(): \ADIOS\Core\Description\Table
  {
    $description = parent::tableDescribe();
    $description->ui['title'] = 'Connected Services';
    $description->ui['addButtonText'] = 'Connect a Service';
    $description->ui['showHeader'] = true;
    $description->ui['showFooter'] = false;
    return $description;
  }

}
