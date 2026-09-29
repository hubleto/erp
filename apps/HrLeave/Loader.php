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

  public function installApp(int $round): void
  {
    if ($round === 1) {
      $this->getModel(Models\LeaveType::class)->upgradeSchema();
      $this->getModel(Models\LeaveBalance::class)->upgradeSchema();
      $this->getModel(Models\LeaveRequest::class)->upgradeSchema();
    }
  }
}