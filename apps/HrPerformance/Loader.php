<?php

namespace Hubleto\App\Community\HrPerformance;

class Loader extends \Hubleto\Erp\App
{
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^hr-performance\/?$/' => ['controller' => Controllers\Performance::class, 'vars' => ['resource' => 'goals']],
      '/^hr-performance\/(?<resource>goals|reviews|courses|learning)(\/(?<recordId>\d+))?\/?$/' => Controllers\Performance::class,
    ]);

    $menu = $this->getService(\Hubleto\App\Community\Desktop\AppMenuManager::class);
    if ($menu) {
      $menu->addItem($this, 'hr-performance', $this->translate('Performance & development'), 'fas fa-chart-line');
    }
  }

  public function renderSecondSidebar(): string
  {
    return '
      ' . $this->secondSidebarTitle() . '
      <div class="app-sidebar-buttons">
        ' . $this->secondSidebarButton('hr-performance/goals', 'fas fa-bullseye', 'Goals') . '
        ' . $this->secondSidebarButton('hr-performance/reviews', 'fas fa-star-half-stroke', 'Reviews') . '
        ' . $this->secondSidebarButton('hr-performance/courses', 'fas fa-book-open', 'Courses') . '
        ' . $this->secondSidebarButton('hr-performance/learning', 'fas fa-graduation-cap', 'Learning assignments') . '
      </div>
    ';
  }

  public function installApp(int $round): void
  {
    if ($round === 1) {
      $this->getModel(Models\Goal::class)->upgradeSchema();
      $this->getModel(Models\Review::class)->upgradeSchema();
      $this->getModel(Models\Course::class)->upgradeSchema();
      $this->getModel(Models\LearningAssignment::class)->upgradeSchema();
    }
  }
}