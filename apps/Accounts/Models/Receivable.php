<?php

namespace Hubleto\App\Community\Accounts\Models;

use Hubleto\App\Community\Journal\Models\EntryLine;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;

class Receivable extends \Hubleto\Erp\Model
{
  public string $table = 'accounts_receivable';
  public string $recordManagerClass = RecordManagers\Receivable::class;
  public ?string $lookupSqlValue = 'concat({%TABLE%}.id_transaction, \' \', \' (\', {%TABLE%}.amount, \')\')';

  public array $relations = [
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'invoice_date' => new Date($this, $this->translate("Invoice date"))->setRequired(),
      'due_date' => new Date($this, $this->translate("Due date"))->setRequired(),
      'amount' => new Integer($this, $this->translate("Amount"))->setRequired(),
      'status' => new Integer($this, $this->translate("Status"))->setEnumValues([
        0 => 'Pending',
        1 => 'Paid',
        2 => 'Overdue',
      ])->setRequired(),
      'description' => new Varchar($this, $this->translate("Description")),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Account Receivables';
    $description->ui['addButtonText'] = 'Add a receivable';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;
    return $description;
  }
}
