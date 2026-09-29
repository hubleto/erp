<?php

namespace Hubleto\App\Community\HrLeave;

class Loader extends \Hubleto\Erp\App
{
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^hr-leave\/?$/' => ['controller' => Controllers\Leave::class, 'vars' => ['resource' => 'requests']],
      '/^hr-leave\/(?<resource>requests|types|balances)(\/(?<recordId>\d+))?\/?$/' => Controllers\Leave::class,
    ]);

    $menu = $this->getService(\Hubleto\App\Community\Desktop\AppMenuManager::class);
    if ($menu) {
      $menu->addItem($this, 'hr-leave', $this->translate('Leave'), 'fas fa-umbrella-beach');
    }

    $calendarManager = $this->getService(\Hubleto\App\Community\Calendar\Manager::class);
    if ($calendarManager) {
      $calendarManager->addCalendar($this, 'hr-leave', Calendar::class);
    }

    $workflowManager = $this->getService(\Hubleto\App\Community\Workflow\Manager::class);
    $workflowManager->addWorkflowGroup($this, 'hr_leave', Workflow::class);
  }

  public function renderSecondSidebar(): string
  {
    return '
      ' . $this->secondSidebarTitle() . '
      <div class="app-sidebar-buttons">
        ' . $this->secondSidebarButton('hr-leave/requests', 'fas fa-file-circle-check', 'Leave requests') . '
        ' . $this->secondSidebarButton('hr-leave/types', 'fas fa-list-check', 'Leave types') . '
        ' . $this->secondSidebarButton('hr-leave/balances', 'fas fa-scale-balanced', 'Entitlements') . '
        ' . $this->secondSidebarButton('calendar?show=hr-leave', 'fas fa-calendar-days', 'Leave calendar') . '
      </div>
    ';
  }

  public function getSidebarBadgeNumber(): int
  {
    $counter = $this->getService(Counter::class);
    return $counter->pendingRequests();
  }

  public function installApp(int $round): void
  {
    if ($round === 1) {
      $this->getModel(Models\LeaveType::class)->upgradeSchema();
      $this->getModel(Models\LeaveBalance::class)->upgradeSchema();
      $this->getModel(Models\LeaveRequest::class)->upgradeSchema();
    }
  }

  public function generateDemoData(): void
  {
    $mUser = $this->getModel(\Hubleto\App\Community\Auth\Models\User::class);
    $user = $mUser->record->where('is_active', true)->orderBy('id')->first();
    if (!$user) return;

    $mLeaveType = $this->getModel(Models\LeaveType::class);
    $leaveTypes = [];
    foreach ([
      ['name' => $this->translate('Annual leave'), 'days' => 25, 'paid' => 1],
      ['name' => $this->translate('Sick leave'), 'days' => 10, 'paid' => 1],
    ] as $typeData) {
      $type = $mLeaveType->record->where('name', $typeData['name'])->first();
      if (!$type) {
        $created = $mLeaveType->record->recordCreate([
          'name' => $typeData['name'],
          'annual_entitlement' => $typeData['days'],
          'is_paid' => $typeData['paid'],
          'requires_approval' => 1,
          'description' => $this->translate('Demo leave policy.'),
        ]);
        $type = $mLeaveType->record->find($created['id']);
      }
      $leaveTypes[] = $type;
    }

    $mBalance = $this->getModel(Models\LeaveBalance::class);
    $mRequest = $this->getModel(Models\LeaveRequest::class);
    $year = (int) date('Y');

    foreach ($leaveTypes as $leaveType) {
      if (!$mBalance->record->where('id_user', $user->id)->where('id_leave_type', $leaveType->id)->where('year', $year)->exists()) {
        $mBalance->record->recordCreate([
          'id_user' => $user->id,
          'id_leave_type' => $leaveType->id,
          'year' => $year,
          'days_entitled' => $leaveType->annual_entitlement,
          'days_carried_over' => 2,
        ]);
      }
    }

    $pendingType = $leaveTypes[0];
    if (!$mRequest->record->where('id_user', $user->id)->where('id_leave_type', $pendingType->id)->where('reason', 'Demo pending leave request')->exists()) {
      $mRequest->record->recordCreate([
        'id_user' => $user->id,
        'id_leave_type' => $pendingType->id,
        'date_from' => date('Y-m-d', strtotime('+14 days')),
        'date_to' => date('Y-m-d', strtotime('+16 days')),
        'balance_year' => $year,
        'days_requested' => 3,
        'status' => $this->translate('Pending'),
        'id_approver' => $user->id,
        'reason' => 'Demo pending leave request',
      ]);
    }

    $approvedType = $leaveTypes[1];
    if (!$mRequest->record->where('id_user', $user->id)->where('id_leave_type', $approvedType->id)->where('reason', 'Demo approved leave request')->exists()) {
      $mRequest->record->recordCreate([
        'id_user' => $user->id,
        'id_leave_type' => $approvedType->id,
        'date_from' => date('Y-m-d', strtotime('-14 days')),
        'date_to' => date('Y-m-d', strtotime('-13 days')),
        'balance_year' => $year,
        'days_requested' => 2,
        'status' => $this->translate('Approved'),
        'id_approver' => $user->id,
        'date_decided' => date('Y-m-d', strtotime('-20 days')),
        'reason' => 'Demo approved leave request',
      ]);
    }
  }
}