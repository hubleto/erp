<?php

namespace Hubleto\App\Community\Events;

class Calendar extends \Hubleto\App\Community\Calendar\Calendar
{
  public function getCalendarConfig(): array
  {
    return [
      'title' => $this->translate('Events'),
      'addNewActivityButtonText' => $this->translate('Add new activity linked to Events'),
      'icon' => 'fas fa-calendar',
      'formComponent' => 'EventsFormActivity',
    ];
  }

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    // Implement your functionality for loading calendar events.

    return [];
  }

}
