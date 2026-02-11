<?php

namespace Hubleto\App\Community\Projects\Models;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Lookup;

class MilestoneReport extends \Hubleto\Erp\Model
{

  public string $table = 'projects_milestone_reports';
  public string $recordManagerClass = RecordManagers\MilestoneReport::class;
  public ?string $lookupSqlValue = 'concat({%TABLE%}.progress_percent, "%, ", {%TABLE%}.summary)';

  public array $relations = [
    'MILESTONE' => [ self::BELONGS_TO, Milestone::class, 'id_milestone', 'id' ],
    'REPORTED_BY' => [ self::BELONGS_TO, User::class, 'id_reported_by', 'id' ],
  ];

  public function describeColumns(): array
  {
    $authProvider = $this->authProvider();

    return array_merge(parent::describeColumns(), [
      'id_milestone' => (new Lookup($this, $this->translate('Milestone'), Milestone::class))->setRequired(),
      'summary' => (new Varchar($this, $this->translate('Summary')))->setDefaultVisible()->setRequired(),
      'details' => (new Text($this, $this->translate('Details'))),
      'progress_percent' => (new Integer($this, $this->translate('Progress')))->setDefaultVisible()->setRequired()->setUnit('%'),
      'date_report' => (new Date($this, $this->translate('Reported on')))->setDefaultVisible()->setRequired()->setDefaultValue(date("Y-m-d")),
      'id_reported_by' => (new Lookup($this, $this->translate('Reported by'), User::class))->setRequired()->setDefaultValue($authProvider->getUserId()),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = 'Add milestone report';
    $description->show(['header']);
    $description->hide(['footer']);
    return $description;
  }

}
