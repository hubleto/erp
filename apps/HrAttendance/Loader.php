<?php

namespace Hubleto\App\Community\HrAttendance;

class Loader extends \Hubleto\Erp\App
{
  public function init(): void
  {
    parent::init();

    $this->router()->crud('hr-attendance', Controllers\Attendances::class);
    $this->router()->crud('hr-attendance/shifts', Controllers\Shifts::class);

    $menu = $this->getService(\Hubleto\App\Community\Desktop\AppMenuManager::class);
    if ($menu) {
      $menu->addItem($this, 'hr-attendance', $this->translate('Attendance'), 'fas fa-clock');
    }

    $workflowManager = $this->getService(\Hubleto\App\Community\Workflow\Manager::class);
    $workflowManager->addWorkflowGroup($this, 'hr_attendance', Workflow::class);
  }

  public function renderSecondSidebar(): string
  {
    return '
      ' . $this->secondSidebarTitle() . '
      <div class="app-sidebar-buttons">
        ' . $this->secondSidebarButton('hr-attendance/shifts', 'fas fa-calendar-day', 'Shift schedule') . '
      </div>
    ';
  }

  public function getSidebarBadgeNumber(): int
  {
    $counter = $this->getService(Counter::class);
    return $counter->unapprovedRecords();
  }

  public function installApp(int $round): void
  {
    if ($round === 1) {
      $this->getModel(Models\Shift::class)->upgradeSchema();
      $this->getModel(Models\Attendance::class)->upgradeSchema();
    }
  }

  public function generateDemoData(): void
  {
    $mUser = $this->getModel(\Hubleto\App\Community\Auth\Models\User::class);
    $user = $mUser->record->where('is_active', true)->orderBy('id')->first();
    if (!$user) return;

    $mShift = $this->getModel(Models\Shift::class);
    $shiftStart = date('Y-m-d', strtotime('+1 day')) . ' 09:00:00';
    if (!$mShift->record->where('id_user', $user->id)->where('date_start', $shiftStart)->exists()) {
      $mShift->record->recordCreate([
        'id_user' => $user->id,
        'date_start' => $shiftStart,
        'date_end' => date('Y-m-d', strtotime('+1 day')) . ' 17:00:00',
        'location' => $this->translate('Head office'),
        'status' => $this->translate('Planned'),
      ]);
    }

    $mRecord = $this->getModel(Models\Attendance::class);
    $workDate = date('Y-m-d', strtotime('-1 day'));
    if (!$mRecord->record->where('id_user', $user->id)->where('date_worked', $workDate)->exists()) {
      $mRecord->record->recordCreate([
        'id_user' => $user->id,
        'date_worked' => $workDate,
        'time_in' => '09:05:00',
        'time_out' => '17:00:00',
        'break_minutes' => 30,
        'is_approved' => 0,
        'notes' => $this->translate('Demo attendance record awaiting approval.'),
      ]);
    }

    foreach ([
      [
        'date' => date('Y-m-d', strtotime('-2 days')),
        'time_in' => '08:55:00',
        'time_out' => '17:10:00',
        'approved' => 1,
      ],
      [
        'date' => date('Y-m-d', strtotime('-3 days')),
        'time_in' => '09:20:00',
        'time_out' => '17:00:00',
        'approved' => 0,
      ],
    ] as $attendanceData) {
      if ($mRecord->record->where('id_user', $user->id)->where('date_worked', $attendanceData['date'])->exists()) continue;

      $mRecord->record->recordCreate([
        'id_user' => $user->id,
        'date_worked' => $attendanceData['date'],
        'time_in' => $attendanceData['time_in'],
        'time_out' => $attendanceData['time_out'],
        'break_minutes' => 30,
        'is_approved' => $attendanceData['approved'],
        'notes' => $this->translate('Demo attendance record.'),
      ]);
    }

    $secondShiftStart = date('Y-m-d', strtotime('+3 days')) . ' 10:00:00';
    if (!$mShift->record->where('id_user', $user->id)->where('date_start', $secondShiftStart)->exists()) {
      $mShift->record->recordCreate([
        'id_user' => $user->id,
        'date_start' => $secondShiftStart,
        'date_end' => date('Y-m-d', strtotime('+3 days')) . ' 18:00:00',
        'location' => $this->translate('Remote'),
        'status' => $this->translate('Planned'),
      ]);
    }
  }
}