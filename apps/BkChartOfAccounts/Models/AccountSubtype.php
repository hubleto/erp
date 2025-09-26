<?php

namespace Hubleto\App\Community\BkChartOfAccounts\Models;

use Hubleto\Framework\Db\Column\Varchar;

class AccountSubtype extends \Hubleto\Erp\Model
{
  public string $table = 'accounting_account_subtype';
  public string $recordManagerClass = RecordManagers\AccountSubtype::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';
  public ?string $lookupUrlAdd = 'chart-of-accounts/account-subtypes/add';
  public ?string $lookupUrlDetail = 'chart-of-accounts/account-subtypes/{%ID%}';

  public array $relations = [
    'ACCOUNT' => [ self::HAS_MANY, Account::class, 'id_account_subtype', 'id'  ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'title' => new Varchar($this, $this->translate("Subtype title"))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Account subtypes';
    $description->ui['addButtonText'] = 'Add Account Subtype';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;
    return $description;
  }
}
