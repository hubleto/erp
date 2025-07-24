<?php

namespace HubletoApp\Community\Events;

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
      '/^events\/?$/' => Controllers\Events::class,
      '/^events\/venues\/?$/' => Controllers\Venues::class,
      '/^events\/speakers\/?$/' => Controllers\Speakers::class,
      '/^events\/attendees\/?$/' => Controllers\Attendees::class,
      '/^events\/settings\/?$/' => Controllers\Settings::class,
      '/^events\/settings\/types\/?$/' => Controllers\Types::class,
    ]);

    // Add placeholder for custom settings.
    // This will be displayed in the Settings app, under the "All settings" card.
    $this->main->apps->community('Settings')->addSetting($this, [
      'title' => 'Event types',
      'icon' => 'fas fa-table',
      'url' => 'events/settings',
    ]);

    // Add placeholder for your app's calendar.
    // $calendarManager = $this->main->apps->community('Calendar')->calendarManager;
    // $calendarManager->addCalendar(
    //   'Events-calendar', // UID of your app's calendar. Will be referenced as "source" when fetching app's events.
    //   '#008000', // your app's calendar color
    //   Calendar::class // your app's Calendar class
    // );

    $appMenu = $this->main->apps->community('Desktop')->appMenu;
    $appMenu->addItem($this, 'events', $this->translate('Events'), 'fas fa-people-group');
    $appMenu->addItem($this, 'events/venues', $this->translate('Venues'), 'fas fa-building-columns');
    $appMenu->addItem($this, 'events/speakers', $this->translate('Speakers'), 'fas fa-user-tie');
    $appMenu->addItem($this, 'events/attendees', $this->translate('Attendees'), 'fas fa-user-tag');
  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      (new Models\Type($this->main))->dropTableIfExists()->install();
      (new Models\Venue($this->main))->dropTableIfExists()->install();
      (new Models\Speaker($this->main))->dropTableIfExists()->install();
      (new Models\Attendee($this->main))->dropTableIfExists()->install();
      (new Models\Event($this->main))->dropTableIfExists()->install();
      (new Models\EventVenue($this->main))->dropTableIfExists()->install();
      (new Models\EventSpeaker($this->main))->dropTableIfExists()->install();
      (new Models\EventAttendee($this->main))->dropTableIfExists()->install();
      (new Models\Agenda($this->main))->dropTableIfExists()->install();
    }
    if ($round == 2) {
      $mType = $this->main->di->create(Models\Type::class);
      $mType->record->recordCreate(['name' => 'Seminar']);
      $mType->record->recordCreate(['name' => 'Workshop']);
      $mType->record->recordCreate(['name' => 'Team building']);
      $mType->record->recordCreate(['name' => 'Conference']);
      $mType->record->recordCreate(['name' => 'Trade show']);
      $mType->record->recordCreate(['name' => 'Trade show']);
      $mType->record->recordCreate(['name' => 'Product launch']);
      $mType->record->recordCreate(['name' => 'Networking']);
      $mType->record->recordCreate(['name' => 'Other']);
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
