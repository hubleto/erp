<?php

namespace Hubleto\App\Community\HrLeave;

class Calendar extends \Hubleto\App\Community\Calendar\Calendar
{
  public function getCalendarConfig(): array
  {
    return [
      'position' => 6,
      'color' => '#b35c1e',
      'title' => $this->translate('Approved employee leave'),
      'icon' => 'fas fa-umbrella-beach',
    ];
  }

  public function loadEvent(int $id): array
  {
    return $this->getModel(Models\LeaveRequest::class)->record->prepareReadQuery()
      ->where('hr_leave_requests.id', $id)
      ->whereHas('WORKFLOW_STEP', fn($query) => $query->where('tag', 'hr-leave-approved'))
      ->first()?->toArray() ?? [];
  }

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    $mRequest = $this->getModel(Models\LeaveRequest::class);
    $requests = $mRequest->record->prepareReadQuery()
      ->whereHas('WORKFLOW_STEP', fn($query) => $query->where('tag', 'hr-leave-approved'))
      ->where('hr_leave_requests.date_from', '<=', $dateEnd)
      ->where('hr_leave_requests.date_to', '>=', $dateStart);

    if (!empty($filter['idUser'])) {
      $requests->where('hr_leave_requests.id_user', (int) $filter['idUser']);
    }

    $events = [];
    foreach ($requests->get() as $request) {
      $events[] = [
        'id' => (int) $request->id,
        'start' => $request->date_from,
        'end' => date('Y-m-d', strtotime($request->date_to . ' +1 day')),
        'allDay' => true,
        'title' => $this->translate('Leave') . ' #' . $request->id,
        'color' => '#b35c1e',
        'source' => 'hr-leave',
        'id_owner' => $request->id_user,
        'url' => 'hr-leave/requests/' . $request->id,
      ];
    }

    return $events;
  }
}