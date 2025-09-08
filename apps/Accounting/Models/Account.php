<?php

namespace Hubleto\App\Community\Accounting\Models;

use Hubleto\App\Community\Customers\Models\Customer;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;

class Account extends \Hubleto\Erp\Model
{
  public string $table = 'accounting_account';
  public string $recordManagerClass = RecordManagers\Account::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public array $relations = [
    'ACCOUNT_TYPE' => [ self::BELONGS_TO, JournalEntryLine::class, 'id_account_type', 'id'  ],
    'ACCOUNT_SUBTYPE' => [ self::BELONGS_TO, AccountSubtype::class, 'id_account_subtype', 'id'  ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => new Varchar($this, $this->translate("Account name"))->setRequired(),
      'id_account_type' => new Lookup($this, $this->translate("Account type"), JournalEntryLine::class)->setDescription('Broad categorization of the account')->setRequired(),
      'id_account_subtype' => new Lookup($this, $this->translate("Account subtype"), AccountSubtype::class)->setDescription('Closer specification of this accounts purpose'),
      'balance' => new Integer($this, $this->translate('Balance'))->setEnumValues([
        1 => "Credit",
        2 => "Debit",
      ])->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Accounts';
    $description->ui['addButtonText'] = 'Add an Account';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;
    return $description;
  }
}
