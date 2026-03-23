<?php

namespace Hubleto\App\Community\Tasks;

class Calendar extends \Hubleto\App\Community\Calendar\Calendar
{
  public function getCalendarConfig(): array
  {
    return [
      'title' => $this->translate('Tasks'),
      'addNewActivityButtonText' => $this->translate('Add new activity linked to Tasks'),
      'icon' => 'fas fa-calendar',
      'formComponent' => 'TasksFormActivity',
    ];
  }

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    // Implement your functionality for loading calendar events.

    return [];
  }

}
