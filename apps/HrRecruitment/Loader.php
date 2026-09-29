<?php

namespace Hubleto\App\Community\HrRecruitment;

class Loader extends \Hubleto\Erp\App
{
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^hr-recruitment\/?$/' => ['controller' => Controllers\Recruitment::class, 'vars' => ['resource' => 'job-openings']],
      '/^hr-recruitment\/(?<resource>job-openings|candidates|applications|interviews)(\/(?<recordId>\d+))?\/?$/' => Controllers\Recruitment::class,
    ]);

    $menu = $this->getService(\Hubleto\App\Community\Desktop\AppMenuManager::class);
    if ($menu) {
      $menu->addItem($this, 'hr-recruitment', $this->translate('Recruitment'), 'fas fa-user-plus');
    }

    $calendarManager = $this->getService(\Hubleto\App\Community\Calendar\Manager::class);
    if ($calendarManager) {
      $calendarManager->addCalendar($this, 'hr-interviews', Calendar::class);
    }

    $workflowManager = $this->getService(\Hubleto\App\Community\Workflow\Manager::class);
    $workflowManager->addWorkflowGroup($this, 'hr_recruitment', Workflow::class);
  }

  public function renderSecondSidebar(): string
  {
    return '
      ' . $this->secondSidebarTitle() . '
      <div class="app-sidebar-buttons">
        ' . $this->secondSidebarButton('hr-recruitment/job-openings', 'fas fa-briefcase', 'Job openings') . '
        ' . $this->secondSidebarButton('hr-recruitment/candidates', 'fas fa-user-tie', 'Candidates') . '
        ' . $this->secondSidebarButton('hr-recruitment/applications', 'fas fa-file-lines', 'Applications') . '
        ' . $this->secondSidebarButton('hr-recruitment/interviews', 'fas fa-comments', 'Interviews') . '
        ' . $this->secondSidebarButton('calendar?show=hr-interviews', 'fas fa-calendar-days', 'Interview calendar') . '
      </div>
    ';
  }

  public function getSidebarBadgeNumber(): int
  {
    $counter = $this->getService(Counter::class);
    return $counter->inProgressApplications();
  }

  public function installApp(int $round): void
  {
    if ($round === 1) {
      $this->getModel(Models\JobOpening::class)->upgradeSchema();
      $this->getModel(Models\Candidate::class)->upgradeSchema();
      $this->getModel(Models\Application::class)->upgradeSchema();
      $this->getModel(Models\Interview::class)->upgradeSchema();
    }
  }

  public function generateDemoData(): void
  {
    $mUser = $this->getModel(\Hubleto\App\Community\Auth\Models\User::class);
    $user = $mUser->record->where('is_active', true)->orderBy('id')->first();
    if (!$user) return;

    $mJob = $this->getModel(Models\JobOpening::class);
    $job = $mJob->record->where('title', $this->translate('People Operations Manager - Demo'))->first();
    if (!$job) {
      $job = $mJob->record->recordCreate([
        'title' => $this->translate('People Operations Manager - Demo'),
        'department' => $this->translate('People Operations'),
        'location' => $this->translate('Hybrid'),
        'employment_type' => $this->translate('Full-time'),
        'status' => $this->translate('Open'),
        'positions' => 1,
        'date_opened' => date('Y-m-d'),
        'id_hiring_manager' => $user->id,
        'description' => $this->translate('Demo opening for an experienced people operations professional.'),
      ]);
      $job = $mJob->record->find($job['id']);
    }

    $mCandidate = $this->getModel(Models\Candidate::class);
    $candidate = $mCandidate->record->where('email', 'alex.morgan.demo@example.test')->first();
    if (!$candidate) {
      $candidate = $mCandidate->record->recordCreate([
        'first_name' => 'Alex',
        'last_name' => 'Morgan',
        'email' => 'alex.morgan.demo@example.test',
        'phone' => '+1 555 010 2040',
        'source' => $this->translate('Careers page'),
        'portfolio_url' => 'https://example.test/alex-morgan',
        'consent_to_store_data' => 1,
        'notes' => $this->translate('Demo candidate profile.'),
      ]);
      $candidate = $mCandidate->record->find($candidate['id']);
    }

    $mApplication = $this->getModel(Models\Application::class);
    $application = $mApplication->record
      ->where('id_job_opening', $job->id)
      ->where('id_candidate', $candidate->id)
      ->first();
    if (!$application) {
      $application = $mApplication->record->recordCreate([
        'id_job_opening' => $job->id,
        'id_candidate' => $candidate->id,
        'stage' => $this->translate('Screening'),
        'status' => $this->translate('In progress'),
        'date_applied' => date('Y-m-d', strtotime('-2 days')),
        'notes' => $this->translate('Demo application awaiting initial review.'),
      ]);
      $application = $mApplication->record->find($application['id']);
    }

    $mInterview = $this->getModel(Models\Interview::class);
    if (!$mInterview->record->where('id_application', $application->id)->exists()) {
      $mInterview->record->recordCreate([
        'id_application' => $application->id,
        'id_interviewer' => $user->id,
        'date_start' => date('Y-m-d H:i:s', strtotime('+5 days 10:00')),
        'date_end' => date('Y-m-d H:i:s', strtotime('+5 days 11:00')),
        'location' => $this->translate('Video call'),
        'status' => $this->translate('Scheduled'),
        'feedback' => '',
      ]);
    }
  }
}