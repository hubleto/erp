<?php

namespace Hubleto\App\Community\HrEmployees;

class Loader extends \Hubleto\Erp\App
{
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^hr-employees\/?$/' => ['controller' => Controllers\Employees::class, 'vars' => ['resource' => 'employees']],
      '/^hr-employees\/(?<resource>employees)(\/(?<recordId>\d+))?\/?$/' => Controllers\Employees::class,
    ]);

    $menu = $this->getService(\Hubleto\App\Community\Desktop\AppMenuManager::class);
    if ($menu) {
      $menu->addItem($this, 'hr-employees', $this->translate('Employees'), 'fas fa-id-card');
    }

    $workflowManager = $this->getService(\Hubleto\App\Community\Workflow\Manager::class);
    $workflowManager->addWorkflowGroup($this, 'hr_employees', Workflow::class);
  }

  public function renderSecondSidebar(): string
  {
    return '
      ' . $this->secondSidebarTitle() . '
      <div class="app-sidebar-buttons">
        ' . $this->secondSidebarButton('hr-employees/employees', 'fas fa-id-card', 'Employees') . '
      </div>
    ';
  }

  public function getSidebarBadgeNumber(): int
  {
    $counter = $this->getService(Counter::class);
    return $counter->activeEmployees();
  }

  public function installApp(int $round): void
  {
    if ($round === 1) {
      $this->getModel(Models\Employee::class)->upgradeSchema();
    }
  }
}