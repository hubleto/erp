<?php

namespace Hubleto\App\Community\EventFeedback;

class Calendar extends \Hubleto\App\Community\Calendar\Calendar
{
  public function getCalendarConfig(): array
  {
    return [
      'title' => $this->translate('EventFeedback'),
      'addNewActivityButtonText' => $this->translate('Add new activity linked to EventFeedback'),
      'icon' => 'fas fa-calendar',
      'formComponent' => 'EventFeedbackFormActivity',
    ];
  }

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    // Implement your functionality for loading calendar events.

    return [];
  }

}
