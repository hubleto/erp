<?php

namespace Hubleto\App\Community\Projects;

class Loader extends \Hubleto\Erp\App
{

  /**
   * Inits the app: adds routes, settings, calendars, event listeners, menu items, ...
   *
   * @return void
   *
   */
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      // '/^projects\/api\/save-junction\/?$/' => Controllers\Api\SaveJunction::class,

      '/^projects\/api\/convert-deal-to-project\/?$/' => Controllers\Api\ConvertDealToProject::class,
      '/^projects\/api\/create-from-order\/?$/' => Controllers\Api\CreateFromOrder::class,
      '/^projects\/api\/get-statistics\/?$/' => Controllers\Api\GetStatistics::class,
      '/^projects\/api\/set-parent-order\/?$/' => Controllers\Api\SetParentOrder::class,

      '/^projects(\/(?<recordId>\d+))?\/?$/' => Controllers\Projects::class,
      '/^projects\/add?\/?$/' => ['controller' => Controllers\Projects::class, 'vars' => [ 'recordId' => -1 ]],

      '/^projects\/task-assignment(\/(?<recordId>\d+))?\/?$/' => Controllers\ProjectsTasks::class,
      '/^projects\/task-assignment\/add?\/?$/' => ['controller' => Controllers\ProjectsTasks::class, 'vars' => [ 'recordId' => -1 ]],

      '/^projects\/milestones(\/(?<recordId>\d+))?\/?$/' => Controllers\Milestones::class,
      '/^projects\/milestones\/add?\/?$/' => ['controller' => Controllers\Milestones::class, 'vars' => [ 'recordId' => -1 ]],

      '/^projects\/monthly-summary\/?$/' => Controllers\MonthlySummary::class,
    ]);

    /** @var \Hubleto\App\Community\Workflow\Manager $workflowManager */
    $workflowManager = $this->getService(\Hubleto\App\Community\Workflow\Manager::class);
    $workflowManager->addWorkflow($this, 'projects', Workflow::class);

    $this->addSearchSwitch('p', 'projects');

    $settingsApp = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
    $settingsApp->addSetting($this, [
      'title' => 'Projects', // or $this->translate('Projects')
      'icon' => 'fas fa-table',
      'url' => 'settings/projects',
    ]);

    /** @var \Hubleto\App\Community\Calendar\Manager $calendarManager */
    $calendarManager = $this->getService(\Hubleto\App\Community\Calendar\Manager::class);
    $calendarManager->addCalendar($this, 'projects', $this->configAsString('calendarColor'), Calendar::class);

  }

  // upgradeSchema
  public function installApp(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Project::class)->upgradeSchema();
      $this->getModel(Models\Milestone::class)->upgradeSchema();
      $this->getModel(Models\MilestoneReport::class)->upgradeSchema();
      $this->getModel(Models\ProjectDeal::class)->upgradeSchema();
      $this->getModel(Models\ProjectOrder::class)->upgradeSchema();
      $this->getModel(Models\ProjectTask::class)->upgradeSchema();
      $this->getModel(Models\ProjectActivity::class)->upgradeSchema();
      $this->getModel(Models\Expense::class)->upgradeSchema();
    }
  }

  // generateDemoData
  public function generateDemoData(): void
  {
    $mProject = $this->getModel(Models\Project::class);

    $mProject->record->recordCreate([
      'id_deal' => 1,
      'title' => $this->translate('Sample project #1'),
      'description' => $this->translate('Sample project #1 for demonstration purposes.'),
      'id_main_developer' => 1,
      'id_account_manager' => 1,
      'id_phase' => 3,
      'color' => '#008000',
    ]);

    $mProject->record->recordCreate([
      'id_deal' => 1,
      'title' => $this->translate('Sample project #2'),
      'description' => $this->translate('Sample project #2 for demonstration purposes.'),
      'id_main_developer' => 1,
      'id_account_manager' => 1,
      'id_phase' => 1,
      'color' => '#008000',
    ]);
  }

  /**
   * [Description for renderSecondSidebar]
   *
   * @return string
   *
   */
  public function renderSecondSidebar(): string
  {
    return '
      <div class="flex flex-col gap-2">
        <a class="btn btn-square btn-primary-outline" href="' . $this->env()->projectUrl . '/projects">
          <span class="icon"><i class="fas fa-diagram-project"></i></span>
          <span class="text">' . $this->translate('Projects') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/projects/milestones">
          <span class="icon"><i class="fas fa-calendar-check"></i></span>
          <span class="text">' . $this->translate('Milestones') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/projects/task-assignment">
          <span class="icon"><i class="fas fa-check-double"></i></span>
          <span class="text">' . $this->translate('Task assignment') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/projects/monthly-summary">
          <span class="icon"><i class="fas fa-chart-bar"></i></span>
          <span class="text">' . $this->translate('Monthly summary') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/calendar?show=projects">
          <span class="icon"><i class="fas fa-calendar-days"></i></span>
          <span class="text">' . $this->translate('Calendar') . '</span>
        </a>
      </div>
    ';
  }

  /**
   * Implements fulltext search functionality for tasks
   *
   * @param array $expressions List of expressions to be searched and glued with logical 'or'.
   *
   * @return array
   *
   */
  public function search(array $expressions): array
  {
    $mProject = $this->getModel(Models\Project::class);
    $qProjects = $mProject->record->prepareReadQuery();

    foreach ($expressions as $e) {
      $qProjects = $qProjects->where(function($q) use ($e) {
        $q->orWhere('projects.identifier', 'like', '%' . $e . '%');
        $q->orWhere('projects.title', 'like', '%' . $e . '%');
      })
      ->where('projects.is_closed', false);
    }

    $projects = $qProjects->get()->toArray();

    $results = [];

    foreach ($projects as $project) {
      $results[] = [
        "id" => $project['id'],
        "label" => $project['identifier'] . ' ' . $project['title'],
        "url" => 'projects/' . $project['id'],
      ];
    }

    return $results;
  }

}
