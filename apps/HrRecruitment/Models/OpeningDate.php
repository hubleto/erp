<?php

namespace Hubleto\App\Community\HrRecruitment\Models;

use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Varchar;

class OpeningDate extends \Hubleto\Erp\Model
{
  public string $table = 'hr_recruitment_opening_dates';
  public string $recordManagerClass = RecordManagers\OpeningDate::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Label')))->setDefaultVisible()->setRequired(),
      'opened_on' => (new Date($this, $this->translate('Opened on')))->setDefaultVisible()->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = $this->translate('Add opening date');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }
}