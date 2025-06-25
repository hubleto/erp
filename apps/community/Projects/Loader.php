<?php

namespace HubletoApp\Community\Projects;

class Loader extends \HubletoMain\Core\App
{

  // Uncomment following if you want a button for app's settings
  // to be rendered next in sidebar, right next to your app's button.
  // public bool $hasCustomSettings = true;

  // init
  public function init(): void
  {
    parent::init();

    // Add app routes.
    // By default, each app should have a welcome dashboard.
    // If your app will have own settings panel, it should be under the `settings/your-app` slug.
    $this->main->router->httpGet([
      '/^projects\/?$/' => Controllers\Dashboard::class,
      '/^projects\/contacts\/?$/' => Controllers\Contacts::class,
      '/^settings\/projects\/?$/' => Controllers\Settings::class,
    ]);

    $this->main->router->httpGet([ '/^projects\/?$/' => Controllers\Projects::class ]);
    $this->main->router->httpGet([ '/^projects\/phases\/?$/' => Controllers\Phases::class ]);

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

    // Uncomment following to configure your app's menu
    // $appMenu = $this->main->apps->community('Desktop')->appMenu;
    // $appMenu->addItem($this, 'projects/item-1', $this->translate('Item 1'), 'fas fa-table');
    // $appMenu->addItem($this, 'projects/item-2', $this->translate('Item 2'), 'fas fa-list');
  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      (new Models\Phase($this->main))->dropTableIfExists()->install();
      (new Models\Project($this->main))->dropTableIfExists()->install();
    }
    if ($round == 2) {
      $mPhase = new Models\Phase($this->main);
      $mPhase->record->recordCreate(['name' => 'Early preparation', 'order' => 1, 'color' => '#344556']);
      $mPhase->record->recordCreate(['name' => 'Advanced preparation', 'order' => 2, 'color' => '#6830a5']);
      $mPhase->record->recordCreate(['name' => 'Final preparation', 'order' => 3, 'color' => '#3068a5']);
      $mPhase->record->recordCreate(['name' => 'Early implementation', 'order' => 4, 'color' => '#ae459f']);
      $mPhase->record->recordCreate(['name' => 'Advanced implementation', 'order' => 5, 'color' => '#a38f9a']);
      $mPhase->record->recordCreate(['name' => 'Final implementation', 'order' => 6, 'color' => '#44879a']);
      $mPhase->record->recordCreate(['name' => 'Delivery', 'order' => 7, 'color' => '#74809a']);
    }
    if ($round == 3) {
      // do something in the 3rd round, if required
    }
  }

  // generateDemoData
  public function generateDemoData(): void
  {
    $mProject = new Models\Project($this->main);

    $mProject->record->recordCreate([
      'title' => 'Sample project',
      'identifier' => 'SMP-1',
      'description' => 'Sample project for demonstration purposes.',
      'id_main_developer' => 1,
      'id_account_manager' => 1,
      'id_phase' => 3,
      'color' => '#008000',
    ]);
  }

}