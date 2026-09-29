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

  public function generateDemoData(): void
  {
    $mUser = $this->getModel(\Hubleto\App\Community\Auth\Models\User::class);
    $user = $mUser->record->where('is_active', true)->orderBy('id')->first();
    if (!$user) return;

    $mEmployee = $this->getModel(Models\Employee::class);
    if ($mEmployee->record->where('id_user', $user->id)->exists()) return;

    $mTeam = $this->getModel(\Hubleto\App\Community\Settings\Models\Team::class);
    $team = $mTeam->record->orderBy('id')->first();

    $mEmployee->record->recordCreate([
      'id_user' => $user->id,
      'employee_number' => 'DEMO-' . $user->id,
      'job_title' => $user->position ?: $this->translate('People Operations Specialist'),
      'id_team' => $team?->id,
      'id_manager' => null,
      'employment_type' => $this->translate('Full-time'),
      'employment_status' => $this->translate('Active'),
      'date_hired' => date('Y-m-d', strtotime('-2 years')),
      'work_location' => $this->translate('Head office'),
      'notes' => $this->translate('Demo employee profile.'),
    ]);
  }
}