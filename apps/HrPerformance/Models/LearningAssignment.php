<?php

namespace Hubleto\App\Community\HrPerformance\Models;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;

class LearningAssignment extends \Hubleto\Erp\Model
{
  public string $table = 'hr_learning_assignments';
  public string $recordManagerClass = RecordManagers\LearningAssignment::class;
  public array $relations = [
    'USER' => [self::BELONGS_TO, User::class, 'id_user', 'id'],
    'COURSE' => [self::BELONGS_TO, Course::class, 'id_course', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_user' => (new Lookup($this, $this->translate('Employee'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setRequired(),
      'id_course' => (new Lookup($this, $this->translate('Course'), Course::class))->setDefaultVisible()->setRequired(),
      'date_assigned' => (new Date($this, $this->translate('Assigned')))->setDefaultVisible()->setRequired(),
      'date_due' => (new Date($this, $this->translate('Due date')))->setDefaultVisible(),
      'date_completed' => (new Date($this, $this->translate('Completed'))),
      'status' => (new Varchar($this, $this->translate('Status')))->setDefaultVisible()->setRequired(),
      'notes' => (new Text($this, $this->translate('Notes'))),
    ]);
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();
    $description->inputs['status']->setPredefinedValues([
      $this->translate('Assigned'),
      $this->translate('In progress'),
      $this->translate('Completed'),
      $this->translate('Overdue'),
      $this->translate('Cancelled'),
    ]);
    return $description;
  }

  public function getRelationsIncludedInLoadTableData(): array|null
  {
    return ['USER', 'COURSE'];
  }

  public function getMaxReadLevelForLoadTableData(): int
  {
    return 1;
  }
}