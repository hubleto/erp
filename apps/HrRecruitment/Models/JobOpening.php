<?php

namespace Hubleto\App\Community\HrRecruitment\Models;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;

class JobOpening extends \Hubleto\Erp\Model
{
  public string $table = 'hr_job_openings';
  public string $recordManagerClass = RecordManagers\JobOpening::class;
  public ?string $lookupSqlValue = '{%TABLE%}.title';
  public array $relations = [
    'HIRING_MANAGER' => [self::BELONGS_TO, User::class, 'id_hiring_manager', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'title' => (new Varchar($this, $this->translate('Job title')))->setDefaultVisible()->setRequired(),
      'department' => (new Varchar($this, $this->translate('Department')))->setDefaultVisible(),
      'location' => (new Varchar($this, $this->translate('Location'))),
      'employment_type' => (new Varchar($this, $this->translate('Employment type')))->setDefaultVisible(),
      'status' => (new Varchar($this, $this->translate('Status')))->setDefaultVisible()->setRequired(),
      'positions' => (new Integer($this, $this->translate('Positions')))->setDefaultValue(1),
      'date_opened' => (new Date($this, $this->translate('Opened')))->setDefaultVisible(),
      'date_closed' => (new Date($this, $this->translate('Closed'))),
      'id_hiring_manager' => (new Lookup($this, $this->translate('Hiring manager'), User::class))->setReactComponent('InputUserSelect'),
      'description' => (new Text($this, $this->translate('Description'))),
    ]);
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();
    $description->inputs['status']->setPredefinedValues([
      $this->translate('Draft'),
      $this->translate('Open'),
      $this->translate('On hold'),
      $this->translate('Closed'),
    ]);
    return $description;
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = $this->translate('Job openings');
    $description->ui['addButtonText'] = $this->translate('Add job opening');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }

  public function getRelationsIncludedInLoadTableData(): array|null
  {
    return ['HIRING_MANAGER'];
  }

  public function getMaxReadLevelForLoadTableData(): int
  {
    return 1;
  }
}