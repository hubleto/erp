<?php

namespace HubletoApp\Community\Calendar\Controllers\Boards;

class Reminders extends \HubletoMain\Core\Controllers\Controller {

  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $getCalendarEvents = new \HubletoApp\Community\Calendar\Controllers\Api\GetCalendarEvents($this->main);

    $this->viewParams['today'] = date("Y-m-d");

    $remindersToday = $getCalendarEvents->loadEventsFromMultipleCalendars(date("Y-m-d", strtotime("-1 year")), date("Y-m-d"), ['completed' => false]);
    $this->viewParams['remindersToday'] = $remindersToday;
 
    $dateTomorrow = date("Y-m-d", time() + 24*3600);
    $remindersTomorrow = $getCalendarEvents->loadEventsFromMultipleCalendars($dateTomorrow, $dateTomorrow, ['completed' => false]);
    $this->viewParams['remindersTomorrow'] = $remindersTomorrow;
 
    $dateLaterStart = date("Y-m-d", time() + 24*3600 * 2);
    $dateLaterEnd = date("Y-m-d", time() + 24*3600 * 7);
    $remindersLater = $getCalendarEvents->loadEventsFromMultipleCalendars($dateLaterStart, $dateLaterEnd, ['completed' => false]);
    $this->viewParams['remindersLater'] = $remindersLater;
 
    $this->setView('@HubletoApp:Community:Calendar/Boards/Reminders.twig');
  }

}