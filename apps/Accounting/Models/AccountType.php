<?php

namespace Hubleto\App\Community\Accounting\Models;

use Hubleto\Framework\Db\Column\Varchar;

class AccountType extends \Hubleto\Erp\Model
{
  public string $table = 'accounting_account_type';
  public string $recordManagerClass = RecordManagers\AccountType::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';
  public ?string $lookupUrlAdd = '/accounting/account-type/add';
  public ?string $lookupUrlDetail = '/accounting/account-type/{%ID%}';

  public array $relations = [
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'title' => new Varchar($this, $this->translate("Type title"))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Account Types';
    $description->ui['addButtonText'] = 'Add Account Type';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;
    return $description;
  }
}
