<?php

namespace HubletoApp\Community\Goals\Models;

use \ADIOS\Core\Db\Column\Lookup;
use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Date;
use \ADIOS\Core\Db\Column\Text;
use \ADIOS\Core\Db\Column\Decimal;
use \ADIOS\Core\Db\Column\Boolean;
use ADIOS\Core\Db\Column\Integer;

use HubletoApp\Community\Settings\Models\Pipeline;
use HubletoApp\Community\Settings\Models\User;

class Goal extends \HubletoMain\Core\Model
{

  public string $table = 'goals';
  public string $eloquentClass = Eloquent\Goal::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';

  public array $relations = [
    'VALUES' => [ self::HAS_MANY, GoalValue::class, 'id_goal', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'title' => (new Varchar($this, $this->translate('Title')))->setRequired(),
      'id_user' => (new Lookup($this, $this->translate('User'), User::class, 'CASCADE'))->setRequired(),
      'id_pipeline' => (new Lookup($this, $this->translate('Pipeline'), Pipeline::class))->setFkOnUpdate('CASCADE')->setFkOnDelete('SET NULL')->setRequired(),
      'frequency' => (new Integer($this, $this->translate('Frequency')))->setRequired()
        ->setEnumValues([1 => "Weekly", 2 => "Monthly", 3 => "Yearly"])->setDefaultValue(1),
      'date_start' => (new Date($this, $this->translate('Start Date')))->setRequired(),
      'date_end' => (new Date($this, $this->translate('End Date')))->setRequired(),
      'metric' => (new Integer($this, $this->translate('Metric')))->setRequired()
        ->setEnumValues([1 => "Value", 2 => "Count"])->setDefaultValue(1),
      'value' => (new Decimal($this, $this->translate('Value')))->setDefaultValue(0),
      'is_indiviual_vals' => (new Boolean($this, $this->translate('Set values for individual intervals?')))->setDefaultValue(0),
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
    $description->defaultValues["frequency"] = 1;
    return $description;
  }

  public function onBeforeUpdate(array $record): array
  {
    if ($record["id"] > 0) {
      $mGoalValue = new GoalValue($this->main);
      $mGoalValue->eloquent
        ->where("id_goal", $record["id"])
        ->delete()
      ;
    }

    return $record;
  }
}
