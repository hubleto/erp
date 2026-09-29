<?php

namespace Hubleto\App\Community\HrLeave\Models;

use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;

class LeaveType extends \Hubleto\Erp\Model
{
  public string $table = 'hr_leave_types';
  public string $recordManagerClass = RecordManagers\LeaveType::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Leave type')))->setDefaultVisible()->setRequired(),
      'annual_entitlement' => (new Decimal($this, $this->translate('Annual entitlement (days)')))->setDecimals(2),
      'is_paid' => (new Boolean($this, $this->translate('Paid')))->setDefaultValue(1),
      'requires_approval' => (new Boolean($this, $this->translate('Requires approval')))->setDefaultValue(1),
      'description' => (new Text($this, $this->translate('Description'))),
    ]);
  }
}