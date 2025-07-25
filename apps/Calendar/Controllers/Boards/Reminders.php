<?php

namespace HubletoApp\Community\Calendar\Controllers\Boards;

class Reminders extends \HubletoMain\Controller
{
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    list($remindersToday, $remindersTomorrow, $remindersLater) = $this->hubletoApp->loadRemindersSummary();

    $this->viewParams['today'] = date("Y-m-d");
    $this->viewParams['remindersToday'] = $remindersToday;
    $this->viewParams['remindersTomorrow'] = $remindersTomorrow;
    $this->viewParams['remindersLater'] = $remindersLater;

    $this->setView('@HubletoApp:Community:Calendar/Boards/Reminders.twig');
  }

}
