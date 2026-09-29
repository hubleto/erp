<?php

namespace Hubleto\App\Community\HrRecruitment\Models;

use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;

class WorkLocation extends \Hubleto\Erp\Model
{
  public string $table = 'hr_recruitment_work_locations';
  public string $recordManagerClass = RecordManagers\WorkLocation::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setDefaultVisible()->setRequired(),
      'description' => (new Text($this, $this->translate('Description'))),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = $this->translate('Add work location');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }
}