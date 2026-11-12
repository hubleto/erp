<?php

namespace Hubleto\App\Community\Calendar;

use Hubleto\App\Community\Calendar\Models\Activity;
use Hubleto\App\Community\Calendar\Models\SharedCalendar;

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
      '/^calendar\/api\/get-calendar-events\/?$/' => Controllers\Api\GetCalendarEvents::class,
      '/^calendar\/api\/get-shared-calendars\/?$/' => Controllers\Api\GetSharedCalendars::class,
      '/^calendar\/api\/stop-sharing-calendar\/?$/' => Controllers\Api\StopSharingCalendar::class,
      '/^calendar\/api\/set-initial-view\/?$/' => Controllers\Api\SetInitialView::class,

      '/^calendar\/?$/' => Controllers\Calendar::class,
      '/^calendar(\/(?<key>\w+))?\/ics\/?$/' => Controllers\IcsCalendar::class,
      '/^calendar\/share\/?$/' => Controllers\Share::class,
      '/^calendar\/settings\/?$/' => Controllers\Settings::class,
      '/^calendar\/boards\/reminders\/?$/' => Controllers\Boards\Reminders::class,
    ]);

    /** @var \Hubleto\App\Community\Settings\Loader $settingsApp */
    $settingsApp = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('Calendar'),
      'icon' => 'fas fa-calendar',
      'url' => 'calendar/settings',
    ]);

    $boards = $this->getService(\Hubleto\App\Community\Dashboards\Manager::class);
    $boards->addBoard(
      $this,
      $this->translate('Reminders'),
      'calendar/boards/reminders'
    );

    /** @var \Hubleto\App\Community\Calendar\Manager $calendarManager */
    $calendarManager = $this->getService(\Hubleto\App\Community\Calendar\Manager::class);
    $calendarManager->addCalendar($this, 'calendar', Calendar::class);

  }

  /**
   * [Description for installApp]
   *
   * @param int $round
   * 
   * @return void
   * 
   */
  public function installApp(int $round): void
  {
    if ($round == 1) {
      $mActivity = $this->getModel(Activity::class);
      $mActivity->upgradeSchema();
      $mSharedCalendar = $this->getModel(SharedCalendar::class);
      $mSharedCalendar->upgradeSchema();
    }
  }

  /**
   * [Description for getSidebarBadgeNumber]
   *
   * @return int
   *
   */
  public function getSidebarBadgeNumber(): int
  {
    /** @var Counter */
    $counter = $this->getService(Counter::class);

    return
      $counter->missedIncompleteActivities(null)
    ;
  }

  public function getInitialView(): string
  {
    $initialView = $this->config()->getAsString('apps/Hubleto\\App\\Community\\Calendar/initialView', 'timeGridWeek');
    if (!in_array($initialView, ['timeGridDay', 'timeGridWeek', 'dayGridMonth', 'listYear'])) $initialView = 'timeGridWeek';
    return $initialView;
  }

  public function setInitialView(string $initialView): void
  {
    if (!in_array($initialView, ['timeGridDay', 'timeGridWeek', 'dayGridMonth', 'listYear'])) $initialView = 'timeGridWeek';
    $this->config()->save('apps/Hubleto\\App\\Community\\Calendar/initialView', $initialView);
  }

  /**
   * Ids of users who can view calendars and activities of other users. Administrators choose them in the settings.
   *
   * @return array<int>
   */
  public function getUsersPermittedToViewOtherCalendars(): array
  {
    $savedIds = $this->config()->getAsString('apps/Hubleto\\App\\Community\\Calendar/usersPermittedToViewOtherCalendars', '');

    $idUsers = [];
    foreach (explode(',', $savedIds) as $savedId) {
      $idUser = (int) $savedId;
      if ($idUser > 0) $idUsers[] = $idUser;
    }

    return $idUsers;
  }

  public function setUsersPermittedToViewOtherCalendars(array $idUsers): void
  {
    $validIds = [];
    foreach ($idUsers as $idUser) {
      if ((int) $idUser > 0) $validIds[] = (int) $idUser;
    }

    $configPath = 'apps/Hubleto\\App\\Community\\Calendar/usersPermittedToViewOtherCalendars';
    $this->config()->set($configPath, implode(',', $validIds));
    $this->config()->save($configPath, implode(',', $validIds));
  }

  public function activeUserCanViewOtherCalendars(): bool
  {
    return in_array($this->authProvider()->getUserId(), $this->getUsersPermittedToViewOtherCalendars());
  }

}
