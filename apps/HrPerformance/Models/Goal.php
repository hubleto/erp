<?php

namespace Hubleto\App\Community\HrPerformance\Models;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;

class Goal extends \Hubleto\Erp\Model
{
  public string $table = 'hr_goals';
  public string $recordManagerClass = RecordManagers\Goal::class;
  public array $relations = [
    'USER' => [self::BELONGS_TO, User::class, 'id_user', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_user' => (new Lookup($this, $this->translate('Employee'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setRequired(),
      'title' => (new Varchar($this, $this->translate('Objective')))->setDefaultVisible()->setRequired(),
      'description' => (new Text($this, $this->translate('Description'))),
      'date_due' => (new Date($this, $this->translate('Due date')))->setDefaultVisible(),
      'progress' => (new Integer($this, $this->translate('Progress (%)')))->setDefaultValue(0),
      'status' => (new Varchar($this, $this->translate('Status')))->setDefaultVisible()->setRequired(),
    ]);
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();
    $description->inputs['status']->setPredefinedValues([
      $this->translate('Not started'),
      $this->translate('In progress'),
      $this->translate('Completed'),
      $this->translate('Deferred'),
    ]);
    return $description;
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = $this->translate('Employee goals');
    $description->ui['addButtonText'] = $this->translate('Add goal');
    $description->ui['orderBy'] = 'date_due asc';
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }

  public function getRelationsIncludedInLoadTableData(): array|null
  {
    return ['USER'];
  }

  public function getMaxReadLevelForLoadTableData(): int
  {
    return 1;
  }
}