<?php

namespace Hubleto\App\Community\Projects\Models;

use Hubleto\Framework\Db\Column\Color;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Virtual;

class Milestone extends \Hubleto\Erp\Model
{

  public string $table = 'projects_milestones';
  public string $recordManagerClass = RecordManagers\Milestone::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';

  public array $relations = [
    'PROJECT' => [ self::BELONGS_TO, Project::class, 'id_project', 'id' ],
    'REPORTS' => [ self::HAS_MANY, MilestoneReport::class, 'id_milestone', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_project' => (new Lookup($this, $this->translate('Project'), Project::class))->setRequired(),
      'title' => (new Varchar($this, $this->translate('Title')))->setDefaultVisible()->setRequired(),
      'date_due' => (new Date($this, $this->translate('Due date')))->setDefaultVisible()->setRequired(),
      'expected_output' => (new Text($this, $this->translate('Expected output')))->setDefaultVisible()->setRequired(),
      'description' => (new Text($this, $this->translate('Description of activities')))->setDefaultVisible(),
      'color' => (new Color($this, $this->translate('Color')))->setDefaultVisible(),

      'virt_last_report_date' => (new Virtual($this, $this->translate('Last report')))
        ->setDefaultVisible()
        ->setProperty('sql', '
          select max(pmr.date_report) from projects_milestone_reports pmr
          where pmr.id_milestone = projects_milestones.id
          order by pmr.date_report desc
          limit 1
        ')
        ->setTextAlign('right')
      ,

      'virt_last_progress_percent' => (new Virtual($this, $this->translate('Last progress')))
        ->setUnit('%')
        ->setDefaultVisible()
        ->setProperty('sql', '
          select pmr.progress_percent from projects_milestone_reports pmr
          where pmr.id_milestone = projects_milestones.id
          order by pmr.date_report desc
          limit 1
        ')
        ->setTextAlign('right')
      ,
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    switch ($this->router()->urlParamAsString('view')) {
      case 'briefOverview':
        $description->hide(['header', 'footer']);
        $description->showOnlyColumns(['title', 'date_due', 'virt_last_report_date', 'virt_last_progress_percent']);
        $description->permissions = [false, true, false, false];
      break;
      default:
        $description->ui['addButtonText'] = 'Add milestone';
        $description->show(['header', 'fulltextSearch', 'columnSearch']);
      break;
    }

    return $description;
  }

}
