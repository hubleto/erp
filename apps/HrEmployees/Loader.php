<?php

namespace Hubleto\App\Community\HrEmployees;

class Loader extends \Hubleto\Erp\App
{
  public function init(): void
  {
    parent::init();

    $this->router()->crud('hr-employees', Controllers\Employees::class);
    $this->router()->crud('hr-employees/employment-types', Controllers\EmploymentTypes::class);
    $this->router()->crud('hr-employees/employment-statuses', Controllers\EmploymentStatuses::class);
    $this->router()->crud('hr-employees/work-locations', Controllers\WorkLocations::class);

    $workflowManager = $this->getService(\Hubleto\App\Community\Workflow\Manager::class);
    $workflowManager->addWorkflowGroup($this, 'hr_employees', Workflow::class);
  }

  public function renderSecondSidebar(): string
  {
    return '
      ' . $this->secondSidebarTitle() . '
      <div class="app-sidebar-buttons">
        ' . $this->secondSidebarButton('hr-employees/employment-types', 'fas fa-file-contract', 'Employment types') . '
        ' . $this->secondSidebarButton('hr-employees/employment-statuses', 'fas fa-user-check', 'Employment statuses') . '
        ' . $this->secondSidebarButton('hr-employees/work-locations', 'fas fa-location-dot', 'Work locations') . '
      </div>
    ';
  }

  public function getSidebarBadgeNumber(): int
  {
    /** @var Counter */
    $counter = $this->getService(Counter::class);
    return $counter->activeEmployees();
  }

  public function installApp(int $round): void
  {
    if ($round === 1) {
      $this->getModel(Models\EmploymentType::class)->upgradeSchema();
      $this->getModel(Models\EmploymentStatus::class)->upgradeSchema();
      $this->getModel(Models\WorkLocation::class)->upgradeSchema();
      $this->getModel(Models\Employee::class)->upgradeSchema();
    }
  }

  public function generateDemoData(): void
  {
    $mEmploymentType = $this->getModel(Models\EmploymentType::class);
    foreach ([
      $this->translate('Full-time'),
      $this->translate('Part-time'),
      $this->translate('Contract'),
      $this->translate('Temporary'),
      $this->translate('Internship'),
    ] as $name) {
      if (!$mEmploymentType->record->where('name', $name)->exists()) {
        $mEmploymentType->record->recordCreate(['name' => $name]);
      }
    }

    $mEmploymentStatus = $this->getModel(Models\EmploymentStatus::class);
    foreach ([
      $this->translate('Active'),
      $this->translate('On leave'),
      $this->translate('Ended'),
    ] as $name) {
      if (!$mEmploymentStatus->record->where('name', $name)->exists()) {
        $mEmploymentStatus->record->recordCreate(['name' => $name]);
      }
    }

    $mWorkLocation = $this->getModel(Models\WorkLocation::class);
    foreach ([
      $this->translate('Head office'),
      $this->translate('Remote'),
      $this->translate('Hybrid'),
    ] as $name) {
      if (!$mWorkLocation->record->where('name', $name)->exists()) {
        $mWorkLocation->record->recordCreate(['name' => $name]);
      }
    }

    $mUser = $this->getModel(\Hubleto\App\Community\Auth\Models\User::class);
    $users = $mUser->record->where('is_active', true)->orderBy('id')->limit(3)->get();
    if ($users->isEmpty()) return;

    $mEmployee = $this->getModel(Models\Employee::class);
    $employmentType = $mEmploymentType->record->where('name', $this->translate('Full-time'))->first();
    $employmentStatus = $mEmploymentStatus->record->where('name', $this->translate('Active'))->first();
    $mTeam = $this->getModel(\Hubleto\App\Community\Settings\Models\Team::class);
    $team = $mTeam->record->orderBy('id')->first();

    $managerId = $users->first()->id;
    foreach ($users as $index => $user) {
      if ($mEmployee->record->where('id_user', $user->id)->exists()) continue;

      $mEmployee->record->recordCreate([
        'id_user' => $user->id,
        'employee_number' => 'DEMO-' . $user->id,
        'job_title' => $user->position ?: $this->translate($index === 0 ? 'People Operations Manager' : 'People Operations Specialist'),
        'id_team' => $team?->id,
        'id_manager' => $user->id == $managerId ? null : $managerId,
        'id_employment_type' => $employmentType->id,
        'id_employment_status' => $employmentStatus->id,
        'date_hired' => date('Y-m-d', strtotime('-' . (24 + $index) . ' months')),
        'id_work_location' => $mWorkLocation->record->where('name', $this->translate($index === 1 ? 'Remote' : 'Head office'))->value('id'),
        'notes' => $this->translate('Demo employee profile.'),
      ]);
    }
  }
}