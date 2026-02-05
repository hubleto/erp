<?php

namespace Hubleto\App\Community\Events;

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

    // Add app routes.
    // By default, each app should have a welcome dashboard.
    // If your app will have own settings panel, it should be under the `settings/your-app` slug.
    $this->router()->get([
      '/^events\/?$/' => Controllers\Events::class,
      '/^events\/venues\/?$/' => Controllers\Venues::class,
      '/^events\/speakers\/?$/' => Controllers\Speakers::class,
      '/^events\/attendees\/?$/' => Controllers\Attendees::class,
      '/^events\/settings\/?$/' => Controllers\Settings::class,
      '/^events\/settings\/types\/?$/' => Controllers\Types::class,
    ]);

    // Add placeholder for custom settings.
    // This will be displayed in the Settings app, under the "All settings" card.
    $settingsApp = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
    $settingsApp->addSetting($this, [
      'title' => 'Event types',
      'icon' => 'fas fa-table',
      'url' => 'events/settings',
    ]);

    $appMenu = $this->getService(\Hubleto\App\Community\Desktop\AppMenuManager::class);
    $appMenu->addItem($this, 'events', $this->translate('Events'), 'fas fa-people-group');
    $appMenu->addItem($this, 'events/venues', $this->translate('Venues'), 'fas fa-building-columns');
    $appMenu->addItem($this, 'events/speakers', $this->translate('Speakers'), 'fas fa-user-tie');
    $appMenu->addItem($this, 'events/attendees', $this->translate('Attendees'), 'fas fa-user-tag');
  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Type::class)->dropTableIfExists()->install();
      $this->getModel(Models\Venue::class)->dropTableIfExists()->install();
      $this->getModel(Models\Speaker::class)->dropTableIfExists()->install();
      $this->getModel(Models\Attendee::class)->dropTableIfExists()->install();
      $this->getModel(Models\Event::class)->dropTableIfExists()->install();
      $this->getModel(Models\EventVenue::class)->dropTableIfExists()->install();
      $this->getModel(Models\EventSpeaker::class)->dropTableIfExists()->install();
      $this->getModel(Models\EventAttendee::class)->dropTableIfExists()->install();
      $this->getModel(Models\Agenda::class)->dropTableIfExists()->install();
    }
    if ($round == 2) {
      $mType = $this->getModel(Models\Type::class);
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
