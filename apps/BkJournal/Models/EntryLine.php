<?php

namespace Hubleto\App\Community\BkJournal\Models;

use Hubleto\App\Community\BkChartOfAccounts\Models\Account;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;

class EntryLine extends \Hubleto\Erp\Model
{
  public string $table = 'journal_entry_line';
  public string $recordManagerClass = RecordManagers\EntryLine::class;
  public ?string $lookupSqlValue = 'concat({%TABLE%}.memo, \' \', \' (\', {%TABLE%}.amount, \')\')';
  public ?string $lookupUrlAdd = '/journal/entries/add';
  public ?string $lookupUrlDetail = '/journal/entries/{%ID%}';

  public array $relations = [
    'JOURNAL_ENTRY' => [ self::BELONGS_TO, Entry::class, 'id', 'id_entry'  ],
    'ACCOUNT' => [ self::BELONGS_TO, Account::class, 'id', 'id_account'  ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'memo' => new Varchar($this, $this->translate("Memo")),
      'type' => new Varchar($this, $this->translate("Type"))->setEnumValues([
        'debit' => 'Debit',
        'credit' => 'Credit',
      ])->setRequired(),
      'amount' => new Integer($this, $this->translate("Amount"))->setRequired(),
      'id_account' => new Lookup($this, $this->translate("Account"), Account::class)->setRequired(),
      'id_entry' => new Lookup($this, $this->translate("Journal Entry"), Entry::class)->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Journal Entry Lines';
    $description->ui['addButtonText'] = 'Add Journal Entry Line';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;
    return $description;
  }
}
