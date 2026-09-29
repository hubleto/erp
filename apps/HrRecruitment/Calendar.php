<?php

namespace Hubleto\App\Community\HrRecruitment;

class Calendar extends \Hubleto\App\Community\Calendar\Calendar
{
  public function getCalendarConfig(): array
  {
    return [
      'position' => 5,
      'color' => '#26734d',
      'title' => $this->translate('Recruitment interviews'),
      'icon' => 'fas fa-user-plus',
    ];
  }

  public function loadEvent(int $id): array
  {
    return $this->getModel(Models\Interview::class)->record->prepareReadQuery()
      ->where('hr_interviews.id', $id)
      ->first()?->toArray() ?? [];
  }

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    $mInterview = $this->getModel(Models\Interview::class);
    $interviews = $mInterview->record->prepareReadQuery()
      ->where('hr_interviews.status', '!=', $this->translate('Cancelled'))
      ->where('hr_interviews.date_start', '<', $dateEnd)
      ->where(function ($query) use ($dateStart) {
        $query->where('hr_interviews.date_end', '>=', $dateStart)
          ->orWhere(function ($query) use ($dateStart) {
            $query->whereNull('hr_interviews.date_end')->where('hr_interviews.date_start', '>=', $dateStart);
          });
      });

    if (!empty($filter['idUser'])) {
      $interviews->where('hr_interviews.id_interviewer', (int) $filter['idUser']);
    }

    $events = [];
    foreach ($interviews->get() as $interview) {
      $events[] = [
        'id' => (int) $interview->id,
        'start' => $interview->date_start,
        'end' => $interview->date_end ?: $interview->date_start,
        'title' => $this->translate('Interview') . ' #' . $interview->id,
        'color' => '#26734d',
        'source' => 'hr-interviews',
        'id_owner' => $interview->id_interviewer,
        'url' => 'hr-recruitment/interviews/' . $interview->id,
      ];
    }

    return $events;
  }
}