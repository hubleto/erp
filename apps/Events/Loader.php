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

  // upgradeSchema
  public function installApp(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Type::class)->upgradeSchema();
      $this->getModel(Models\Venue::class)->upgradeSchema();
      $this->getModel(Models\Speaker::class)->upgradeSchema();
      $this->getModel(Models\Attendee::class)->upgradeSchema();
      $this->getModel(Models\Event::class)->upgradeSchema();
      $this->getModel(Models\EventVenue::class)->upgradeSchema();
      $this->getModel(Models\EventSpeaker::class)->upgradeSchema();
      $this->getModel(Models\EventAttendee::class)->upgradeSchema();
      $this->getModel(Models\Agenda::class)->upgradeSchema();
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
