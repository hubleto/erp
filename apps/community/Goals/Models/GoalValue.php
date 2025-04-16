<?php

namespace HubletoApp\Community\Goals\Models;

use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Date;
use \ADIOS\Core\Db\Column\Text;
use \ADIOS\Core\Db\Column\Decimal;
use \ADIOS\Core\Db\Column\Boolean;
use ADIOS\Core\Db\Column\Integer;

class GoalValue extends \HubletoMain\Core\Model
{
  public string $table = 'goal_values';
  public string $recordManagerClass = RecordManagers\GoalValue::class;
  public ?string $lookupSqlValue = '{%TABLE%}.value';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_goal' => (new Lookup($this, $this->translate('Goal'), Goal::class, 'CASCADE'))->setRequired(),
      'key' => (new Varchar($this, $this->translate('Goal')))->setRequired(),
      'frequency' => (new Integer($this, $this->translate('Frequency')))->setRequired()
        ->setEnumValues([1 => "Weekly", 2 => "Monthly", 3 => "Yearly"]),
      'date_start' => (new Date($this, $this->translate('Start Date')))->setRequired(),
      'date_end' => (new Date($this, $this->translate('End Date')))->setRequired(),
      'value' => (new Decimal($this, $this->translate('Value')))->setRequired(),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();
    return $description;
  }

  public function describeForm(): \ADIOS\Core\Description\Form
  {
    $description = parent::describeForm();
    return $description;
  }
}
