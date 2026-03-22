<?php

namespace Hubleto\App\Community\Projects\Models;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Virtual;

class Expense extends \Hubleto\Erp\Model
{

  public string $table = 'projects_expenses';
  public string $recordManagerClass = RecordManagers\Expense::class;
  public ?string $lookupSqlValue = '{%TABLE%}.reason';

  public array $relations = [
    'PROJECT' => [ self::BELONGS_TO, Project::class, 'id_project', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_project' => (new Lookup($this, $this->translate('Project'), Project::class))->setRequired(),
      'reason' => (new Varchar($this, $this->translate('Reason')))->setDefaultVisible()->setRequired(),
      'date' => (new Date($this, $this->translate('Date')))->setDefaultVisible()->setRequired()->setDefaultValue(date("Y-m-d")),
      'amount' => (new Decimal($this, $this->translate('Amount')))->setDefaultVisible(),
      'id_approved_by' => (new Lookup($this, $this->translate('Approved by'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible(),
      'id_spent_by' => (new Lookup($this, $this->translate('Spent by'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = $this->translate('Add expense');
    $description->show(['header', 'fulltextSearch', 'columnSearch']);
    return $description;
  }

}
