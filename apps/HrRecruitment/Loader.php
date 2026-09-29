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