<?php

namespace Hubleto\App\Community\EventRegistrations;

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
      '/^events-registrations\/?$/' => Controllers\Dashboard::class,
      '/^events-registrations\/contacts\/?$/' => Controllers\Contacts::class,
      '/^settings\/events-registrations\/?$/' => Controllers\Settings::class,
    ]);

    $settingsApp = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('EventRegistrations'),
      'icon' => 'fas fa-table',
      'url' => 'settings/events-registrations',
    ]);

    /** @var \Hubleto\App\Community\Calendar\Manager $calendarManager */
    $calendarManager = $this->getService(\Hubleto\App\Community\Calendar\Manager::class);
    $calendarManager->addCalendar(
      $this,
      'EventRegistrations-calendar', // UID of your app's calendar. Will be referenced as "source" when fetching app's events.
      Calendar::class // your app's Calendar class
    );
  }

  // upgradeSchema
  public function installApp(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Contact::class)->upgradeSchema();
    }
  }

  // generateDemoData
  public function generateDemoData(): void
  {
    // Create any demo data to promote your app.
  }

}
