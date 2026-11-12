<?php

namespace Hubleto\App\Community\Calendar\Controllers;

use Hubleto\App\Community\Calendar\Counter;
use Hubleto\App\Community\Calendar\Manager;
use Hubleto\App\Community\Auth\Models\User;

class Calendar extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      // [ 'url' => 'calendar', 'content' => $this->translate('Calendar') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $show = $this->router()->urlParamAsString('show');

    /** @var Counter */
    $counter = $this->getService(Counter::class);

    /** @var \Hubleto\App\Community\Calendar\Loader */
    $calendarApp = $this->getService(\Hubleto\App\Community\Calendar\Loader::class);

    $this->viewParams['initialView'] = $calendarApp->getInitialView();

    // Users whose calendars the active user can choose to view. Empty if the active user is not permitted.
    $this->viewParams['usersWithViewableCalendars'] = [];
    if ($calendarApp->activeUserCanViewOtherCalendars()) {
      /** @var User $mUser */
      $mUser = $this->getModel(User::class);
      $activeUsers = $mUser->record->where('is_active', true)->orderBy('email')->get();

      foreach ($activeUsers as $user) {
        if ($user->id == $this->authProvider()->getUserId()) continue;

        $fullName = trim($user->first_name . ' ' . $user->last_name);
        $this->viewParams['usersWithViewableCalendars'][] = [
          'id' => $user->id,
          'name' => $fullName == '' ? $user->email : $fullName,
        ];
      }
    }

    /** @var Manager */
    $calendarManager = $this->getService(Manager::class);

    foreach ($calendarManager->getCalendarsSorted() as $calendarName => $calendar) {
      $calendarConfig = $calendar->getCalendarConfig();
      $calendarConfig['color'] = $calendar->getColor();
      $calendarConfig['show'] = empty($show) || $show == $calendarName;
      $calendarConfig['missedActivities'] = $counter->missedIncompleteActivities([$calendarName]);

      $this->viewParams["calendars"][$calendarName] = $calendarConfig;
    }

    $this->setView('@Hubleto:App:Community:Calendar/Calendar.twig');
  }

}
