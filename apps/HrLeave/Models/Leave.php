<?php

namespace Hubleto\App\Community\HrLeave\Models;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Virtual;

class Leave extends \Hubleto\Erp\Model
{
  public string $table = 'hr_leaves';
  public string $recordManagerClass = RecordManagers\Leave::class;
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
        ->setProperty('sql', 'SELECT COALESCE(SUM(r.days_requested), 0) FROM hr_leave_requests r INNER JOIN workflow_steps s ON s.id = r.id_workflow_step WHERE r.id_user = hr_leaves.id_user AND r.id_leave_type = hr_leaves.id_leave_type AND r.balance_year = hr_leaves.year AND s.tag = "hr-leave-approved"'),
      'virt_days_remaining' => (new Virtual($this, $this->translate('Days remaining')))->setDefaultVisible()
        ->setProperty('sql', '(hr_leaves.days_entitled + hr_leaves.days_carried_over - (SELECT COALESCE(SUM(r.days_requested), 0) FROM hr_leave_requests r INNER JOIN workflow_steps s ON s.id = r.id_workflow_step WHERE r.id_user = hr_leaves.id_user AND r.id_leave_type = hr_leaves.id_leave_type AND r.balance_year = hr_leaves.year AND s.tag = "hr-leave-approved"))'),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = $this->translate('Add leave entitlement');
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);
    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    return parent::describeForm();
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