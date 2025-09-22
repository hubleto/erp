<?php

namespace Hubleto\App\Community\Transactions\Models;

use Hubleto\App\Community\Journal\Models\EntryLine;
use Hubleto\Framework\Db\Column\Lookup;

class Reconciliation extends \Hubleto\Erp\Model
{
  public string $table = 'transactions_reconciliation';
  public string $recordManagerClass = RecordManagers\Reconciliation::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id_transaction';

  public array $relations = [
    'TRANSACTION' => [ self::BELONGS_TO, Transaction::class, 'id_transaction', 'id'  ],
    'JOURNAL_ENTRY_LINE' => [ self::BELONGS_TO, EntryLine::class, 'id_journal_entry_line', 'id'  ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_transaction' => new Lookup($this, $this->translate("Transaction"), Transaction::class)->setRequired(),
      'id_journal_entry_line' => new Lookup($this, $this->translate("Journal entry line"), EntryLine::class)->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Reconciliations';
    $description->ui['addButtonText'] = 'Add a reconciliation';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;
    return $description;
  }
}
