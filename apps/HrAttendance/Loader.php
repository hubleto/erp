<?php

namespace Hubleto\App\Community\HrAttendance;

class Loader extends \Hubleto\Erp\App
{
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^hr-attendance\/?$/' => ['controller' => Controllers\Attendance::class, 'vars' => ['resource' => 'records']],
      '/^hr-attendance\/(?<resource>records|shifts)(\/(?<recordId>\d+))?\/?$/' => Controllers\Attendance::class,
    ]);

    $menu = $this->getService(\Hubleto\App\Community\Desktop\AppMenuManager::class);
    if ($menu) {
      $menu->addItem($this, 'hr-attendance', $this->translate('Attendance'), 'fas fa-clock');
    }
  }

  public function renderSecondSidebar(): string
  {
    return '
      ' . $this->secondSidebarTitle() . '
      <div class="app-sidebar-buttons">
        ' . $this->secondSidebarButton('hr-attendance/records', 'fas fa-clock', 'Attendance records') . '
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
      $this->getModel(Models\AttendanceRecord::class)->upgradeSchema();
    }
  }
}