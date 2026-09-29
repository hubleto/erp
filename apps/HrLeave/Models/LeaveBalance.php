<?php

namespace Hubleto\App\Community\HrLeave\Models;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Virtual;

class LeaveBalance extends \Hubleto\Erp\Model
{
  public string $table = 'hr_leave_balances';
  public string $recordManagerClass = RecordManagers\LeaveBalance::class;
  public array $relations = [
    'USER' => [self::BELONGS_TO, User::class, 'id_user', 'id'],
    'LEAVE_TYPE' => [self::BELONGS_TO, LeaveType::class, 'id_leave_type', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_user' => (new Lookup($this, $this->translate('Employee'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible()->setRequired(),
      'id_leave_type' => (new Lookup($this, $this->translate('Leave type'), LeaveType::class))->setDefaultVisible()->setRequired(),
      'year' => (new Integer($this, $this->translate('Year')))->setDefaultVisible()->setRequired()->setDefaultValue((int) date('Y')),
      'days_entitled' => (new Decimal($this, $this->translate('Entitlement (days)')))->setDecimals(2)->setRequired(),
      'days_carried_over' => (new Decimal($this, $this->translate('Carried over (days)')))->setDecimals(2)->setDefaultValue(0),
      'virt_days_used' => (new Virtual($this, $this->translate('Approved days used')))->setDefaultVisible()
        ->setProperty('sql', 'SELECT COALESCE(SUM(days_requested), 0) FROM hr_leave_requests WHERE hr_leave_requests.id_user = hr_leave_balances.id_user AND hr_leave_requests.id_leave_type = hr_leave_balances.id_leave_type AND hr_leave_requests.balance_year = hr_leave_balances.year AND hr_leave_requests.status = "' . addslashes($this->translate('Approved')) . '"'),
      'virt_days_remaining' => (new Virtual($this, $this->translate('Days remaining')))->setDefaultVisible()
        ->setProperty('sql', '(hr_leave_balances.days_entitled + hr_leave_balances.days_carried_over - (SELECT COALESCE(SUM(days_requested), 0) FROM hr_leave_requests WHERE hr_leave_requests.id_user = hr_leave_balances.id_user AND hr_leave_requests.id_leave_type = hr_leave_balances.id_leave_type AND hr_leave_requests.balance_year = hr_leave_balances.year AND hr_leave_requests.status = "' . addslashes($this->translate('Approved')) . '"))'),
    ]);
  }

  public function getRelationsIncludedInLoadTableData(): array|null
  {
    return ['USER', 'LEAVE_TYPE'];
  }

  public function getMaxReadLevelForLoadTableData(): int
  {
    return 1;
  }
}