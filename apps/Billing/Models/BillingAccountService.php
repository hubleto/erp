<?php

namespace HubletoApp\Community\Billing\Models;

use Hubleto\Framework\Db\Column\Lookup;
use HubletoApp\Community\Services\Models\Service;

class BillingAccountService extends \Hubleto\Framework\Models\Model
{
  public string $table = 'billing_accounts_services';
  public string $recordManagerClass = RecordManagers\BillingAccountService::class;

  public array $relations = [
    'SERVICE' => [ self::BELONGS_TO, Service::class, 'id_service', 'id' ],
    'BILLING_ACCOUNT' => [ self::BELONGS_TO, BillingAccount::class, 'id_billing_account', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_billing_account' => (new Lookup($this, $this->translate("Billing Account"), BillingAccount::class))->setRequired(),
      'id_service' => (new Lookup($this, $this->translate("Service"), Service::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Connected Services';
    $description->ui['addButtonText'] = 'Connect a Service';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;
    return $description;
  }

}
