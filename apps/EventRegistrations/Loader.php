<?php

namespace HubletoApp\Community\EventRegistrations;

class Loader extends \Hubleto\Framework\App
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
      '/^eventregistrations\/?$/' => Controllers\Dashboard::class,
      '/^eventregistrations\/contacts\/?$/' => Controllers\Contacts::class,
      '/^settings\/eventregistrations\/?$/' => Controllers\Settings::class,
    ]);

    // Add placeholder for custom settings.
    // This will be displayed in the Settings app, under the "All settings" card.
    $this->main->apps->community('Settings')->addSetting($this, [
      'title' => 'EventRegistrations', // or $this->translate('EventRegistrations')
      'icon' => 'fas fa-table',
      'url' => 'settings/eventregistrations',
    ]);

    // Add placeholder for your app's calendar.
    $calendarManager = $this->main->apps->community('Calendar')->calendarManager;
    $calendarManager->addCalendar(
      'EventRegistrations-calendar', // UID of your app's calendar. Will be referenced as "source" when fetching app's events.
      '#008000', // your app's calendar color
      Calendar::class // your app's Calendar class
    );

    // Uncomment following to configure your app's menu
    // $appMenu = $this->main->apps->community('Desktop')->appMenu;
    // $appMenu->addItem($this, 'eventregistrations/item-1', $this->translate('Item 1'), 'fas fa-table');
    // $appMenu->addItem($this, 'eventregistrations/item-2', $this->translate('Item 2'), 'fas fa-list');
  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      (new Models\Contact($this->main))->dropTableIfExists()->install();
    }
    if ($round == 2) {
      // do something in the 2nd round, if required
    }
    if ($round == 3) {
      // do something in the 3rd round, if required
    }
  }

  // generateDemoData
  public function generateDemoData(): void
  {
    // Create any demo data to promote your app.
  }

}
