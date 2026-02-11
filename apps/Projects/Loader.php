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

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Project::class)->dropTableIfExists()->install();
      $this->getModel(Models\Milestone::class)->dropTableIfExists()->install();
      $this->getModel(Models\MilestoneReport::class)->dropTableIfExists()->install();
      $this->getModel(Models\ProjectDeal::class)->dropTableIfExists()->install();
      $this->getModel(Models\ProjectOrder::class)->dropTableIfExists()->install();
      $this->getModel(Models\ProjectTask::class)->dropTableIfExists()->install();
      $this->getModel(Models\ProjectActivity::class)->dropTableIfExists()->install();
    }
  }

  // generateDemoData
  public function generateDemoData(): void
  {
    $mProject = $this->getModel(Models\Project::class);

    $mProject->record->recordCreate([
      'id_deal' => 1,
      'title' => 'Sample project #1',
      'description' => 'Sample project #1 for demonstration purposes.',
      'id_main_developer' => 1,
      'id_account_manager' => 1,
      'id_phase' => 3,
      'color' => '#008000',
    ]);

    $mProject->record->recordCreate([
      'id_deal' => 1,
      'title' => 'Sample project #2',
      'description' => 'Sample project #2 for demonstration purposes.',
      'id_main_developer' => 1,
      'id_account_manager' => 1,
      'id_phase' => 1,
      'color' => '#008000',
    ]);
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
