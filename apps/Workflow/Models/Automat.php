<?php

namespace Hubleto\App\Community\Workflow\Models;

use Hubleto\Framework\Db\Column\Json;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Integer;

class Automat extends \Hubleto\Erp\Model
{
  const ENUM_TRIGGERS = [
    'onModelAfterUpdate' => 'onModelAfterUpdate',
  ];

  public string $table = 'workflow_automats';
  public string $recordManagerClass = RecordManagers\Automat::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public array $relations = [
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired()->setDefaultVisible()->setIcon(self::COLUMN_NAME_DEFAULT_ICON),
      'trigger' => (new Varchar($this, $this->translate('Trigger')))->setRequired()->setDefaultVisible()->setEnumValues(self::ENUM_TRIGGERS),
      'execution_order' => (new Integer($this, $this->translate('Execution order')))->setRequired()->setDefaultVisible(),
      'description' => (new Varchar($this, $this->translate('Description')))->setDefaultVisible(),
      'conditions' => (new Json($this, $this->translate('Conditions')))->setDefaultVisible(),
      'actions' => (new Json($this, $this->translate('Actions')))->setDefaultVisible(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    // $description->ui['title'] = 'Workflows';
    $description->ui['addButtonText'] = $this->translate('Add automat');
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

}
