<?php

namespace HubletoApp\Community\Projects;

class Loader extends \Hubleto\Framework\App
{
  // Uncomment following if you want a button for app's settings
  // to be rendered next in sidebar, right next to your app's button.
  // public bool $hasCustomSettings = true;

  // init
  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^projects(\/(?<recordId>\d+))?\/?$/' => Controllers\Projects::class,
      '/^projects\/phases\/?$/' => Controllers\Phases::class,
      '/^projects\/api\/convert-deal-to-project\/?$/' => Controllers\Api\ConvertDealToProject::class,
    ]);

    // Add placeholder for custom settings.
    // This will be displayed in the Settings app, under the "All settings" card.
    $this->main->apps->community('Settings')->addSetting($this, [
      'title' => 'Projects', // or $this->translate('Projects')
      'icon' => 'fas fa-table',
      'url' => 'settings/projects',
    ]);

    // Add placeholder for your app's calendar.
    $calendarManager = $this->main->apps->community('Calendar')->calendarManager;
    $calendarManager->addCalendar(
      'Projects-calendar', // UID of your app's calendar. Will be referenced as "source" when fetching app's events.
      '#008000', // your app's calendar color
      Calendar::class // your app's Calendar class
    );

    $appMenu = $this->main->apps->community('Desktop')->appMenu;
    $appMenu->addItem($this, 'projects', $this->translate('Projects'), 'fas fa-diagram-project');
    $appMenu->addItem($this, 'projects/phases', $this->translate('Phases'), 'fas fa-list');

    $this->main->apps->community('Tasks')?->registerExternalModel($this, Models\Project::class);

  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      (new Models\Phase($this->main))->dropTableIfExists()->install();
      (new Models\Project($this->main))->dropTableIfExists()->install();
    }
    if ($round == 2) {

    }
    if ($round == 3) {
      $mPhase = $this->main->di->create(Models\Phase::class);
      $mPhase->record->recordCreate(['name' => 'Early preparation', 'order' => 1, 'color' => '#344556']);
      $mPhase->record->recordCreate(['name' => 'Advanced preparation', 'order' => 2, 'color' => '#6830a5']);
      $mPhase->record->recordCreate(['name' => 'Final preparation', 'order' => 3, 'color' => '#3068a5']);
      $mPhase->record->recordCreate(['name' => 'Early implementation', 'order' => 4, 'color' => '#ae459f']);
      $mPhase->record->recordCreate(['name' => 'Advanced implementation', 'order' => 5, 'color' => '#a38f9a']);
      $mPhase->record->recordCreate(['name' => 'Final implementation', 'order' => 6, 'color' => '#44879a']);
      $mPhase->record->recordCreate(['name' => 'Delivery', 'order' => 7, 'color' => '#74809a']);
    }
  }

  // generateDemoData
  public function generateDemoData(): void
  {
    $mProject = $this->main->di->create(Models\Project::class);

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

}
