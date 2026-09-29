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