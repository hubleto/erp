<?php

namespace Hubleto\App\Community\Accounting\Models;

use Hubleto\App\Community\Customers\Models\Customer;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;

class Entry extends \Hubleto\Erp\Model
{
  public string $table = 'accounting_journal_entry';
  public string $recordManagerClass = RecordManagers\Entry::class;
  public ?string $lookupSqlValue = '{%TABLE%}.reference';

  public array $relations = [
    'JOURNAL_ENTRY_LINE' => [ self::HAS_MANY, EntryLine::class, 'id_entry', 'id'  ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'description' => new Varchar($this, $this->translate("Description")),
      'date' => new Date($this, $this->translate("Date"))->setRequired(),
      'reference' => new Varchar($this, $this->translate("Reference number"))->setDescription('Reference number of a corresponding check or invoice')->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Journal Entries';
    $description->ui['addButtonText'] = 'Add Journal Entry';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;
    return $description;
  }

}
