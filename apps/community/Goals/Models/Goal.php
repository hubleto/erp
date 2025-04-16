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
  public string $recordManagerClass = RecordManagers\Goal::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';

  public array $relations = [
    'GOALS' => [ self::HAS_MANY, GoalValue::class, 'id_goal', 'id'],
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
      'goal' => (new Decimal($this, $this->translate('Goal')))->setDefaultValue(0),
      'is_individual_goals' => (new Boolean($this, $this->translate('Set individual goals?')))->setDefaultValue(0),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();

    $description->ui["addButtonText"] = $this->translate('Add Goal');
    $description->ui["title"] = $this->translate('Goals');
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    unset($description->columns['id_pipeline']);
    unset($description->columns['goal']);
    unset($description->columns['is_individual_goals']);

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
    if ($record["id"] > 0 && $this->main->urlParamAsBool('deleteIntervals') == true) {
      $mGoalValue = new GoalValue($this->main);
      $mGoalValue->record
        ->where("id_goal", $record["id"])
        ->recordDelete()
      ;
    }

    return $record;
  }
}
