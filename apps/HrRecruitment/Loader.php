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
}