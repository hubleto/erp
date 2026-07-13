<?php

namespace Hubleto\App\Community\Calendar\Controllers\Boards;

use \Hubleto\App\Community\Calendar\Events;

class Reminders extends \Hubleto\Erp\Controller
{
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    /** @var Events */
    $events = $this->getService(Events::class);
    list($remindersToday, $remindersTomorrow, $remindersLater) = $events->loadRemindersSummary($this->authProvider()->getUserId());

    $this->viewParams['today'] = date("Y-m-d");
    $this->viewParams['remindersToday'] = $remindersToday;
    $this->viewParams['remindersTomorrow'] = $remindersTomorrow;
    $this->viewParams['remindersLater'] = $remindersLater;

    $this->setView('@Hubleto:App:Community:Calendar/Boards/Reminders.twig');
  }

}
