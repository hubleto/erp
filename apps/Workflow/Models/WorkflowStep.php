<?php

namespace Hubleto\App\Community\Workflow\Models;

use Hubleto\Framework\Db\Column\Color;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\App\Community\Deals\Models\Deal;

class WorkflowStep extends \Hubleto\Erp\Model
{
  public string $table = 'workflow_steps';
  public string $recordManagerClass = RecordManagers\WorkflowStep::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public array $relations = [
    'WORKFLOW' => [ self::BELONGS_TO, Workflow::class, 'id_workflow', 'id' ]
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_workflow' => (new Lookup($this, $this->translate("Workflow"), Workflow::class))->setRequired(),
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired(),
      'order' => (new Integer($this, $this->translate('Order')))->setRequired(),
      'color' => (new Color($this, $this->translate('Color')))->setRequired(),
      'tag' => (new Varchar($this, $this->translate('Tag'))),
      'probability' => (new Integer($this, $this->translate('Probability')))->setUnit("%"),
      // 'set_result' => (new Integer($this, $this->translate('Set result of a deal to')))
      //   ->setEnumValues([Deal::RESULT_UNKNOWN => "Unknown", Deal::RESULT_WON => "Won",  Deal::RESULT_LOST => "Lost"])
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Workflow Steps';
    $description->ui['addButtonText'] = 'Add Workflow Step';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

}
