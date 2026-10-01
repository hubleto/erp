<?php

namespace Hubleto\App\Community\LinkedinMessages;

class Calendar extends \Hubleto\App\Community\Calendar\Calendar
{
  public function getCalendarConfig(): array
  {
    return [
      'position' => 5,
      'color' => '#0a66c2',
      'title' => $this->translate('LinkedIn follow-ups'),
      'addNewActivityButtonText' => $this->translate('Add new follow-up linked to a LinkedIn message'),
      'icon' => 'fab fa-linkedin',
      'formComponent' => 'MessageCalendarActivityForm',
    ];
  }

  public function loadEvent(int $id): array
  {
    return $this->prepareLoadActivityQuery($this->getModel(Models\MessageActivity::class), $id)->first()?->toArray();
  }

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = [], $idUser = 0): array
  {
    $idMessage = $this->router()->urlParamAsInteger('idMessage');
    $mMessageActivity = $this->getModel(Models\MessageActivity::class);
    $activities = $this->prepareLoadActivitiesQuery($mMessageActivity, $dateStart, $dateEnd, $filter)->with('MESSAGE.PROFILE');
    if ($idMessage > 0) {
      $activities = $activities->where('id_message', $idMessage);
    }

    $events = $this->convertActivitiesToEvents(
      'linkedin-messages',
      $activities->get()?->toArray(),
      function (array $activity) {
        if (isset($activity['MESSAGE'])) {
          $profile = $activity['MESSAGE']['PROFILE'] ?? null;
          $who = $profile ? trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? '')) : '';
          return 'LinkedIn' . ($who !== '' ? ': ' . $who : '');
        } else {
          return '';
        }
      }
    );

    return $events;
  }

}
