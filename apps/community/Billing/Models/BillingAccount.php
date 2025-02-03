<?php

namespace HubletoApp\Community\Billing\Models;

use HubletoApp\Community\Customers\Models\Company;

use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Varchar;

class BillingAccount extends \HubletoMain\Core\Model
{
  public string $table = 'billing_accounts';
  public string $eloquentClass = Eloquent\BillingAccount::class;
  public ?string $lookupSqlValue = '{%TABLE%}.description';

  public array $relations = [
    'SERVICES' => [ self::HAS_MANY, BillingAccountService::class, 'id_billing_account', 'id' ],
    'COMPANY' => [ self::BELONGS_TO, Company::class, 'id_company', 'id'  ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'id_company' => (new Lookup($this, $this->translate("Company"), Company::class, 'CASCADE'))->setRequired(),
      'description' => (new Varchar($this, $this->translate("Description")))->setRequired(),
    ]));
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Billing Account';
    $description->ui['addButtonText'] = 'Add Billing Account';
    $description->ui['showHeader'] = true;
    $description->ui['showFooter'] = false;
    return $description;
  }
}
