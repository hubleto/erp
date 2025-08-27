<?php

namespace HubletoApp\Community\Calendar\Controllers\Boards;

class Reminders extends \HubletoMain\Controller
{
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $events = $this->getService(\HubletoApp\Community\Calendar\Events::class);
    list($remindersToday, $remindersTomorrow, $remindersLater) = $events->loadRemindersSummary();

    $this->viewParams['today'] = date("Y-m-d");
    $this->viewParams['remindersToday'] = $remindersToday;
    $this->viewParams['remindersTomorrow'] = $remindersTomorrow;
    $this->viewParams['remindersLater'] = $remindersLater;

    $this->setView('@HubletoApp:Community:Calendar/Boards/Reminders.twig');
  }

}
