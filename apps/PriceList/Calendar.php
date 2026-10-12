<?php

namespace Hubleto\App\Community\PriceList;

class Calendar extends \Hubleto\App\Community\Calendar\Calendar
{

  public function getCalendarConfig(): array
  {
    return array_merge(parent::getCalendarConfig(), [
      'position' => 40,
      'color' => '#0d9488',
      'title' => $this->translate('Price lists'),
      'icon' => 'fas fa-tags',
    ]);
  }

  /** Price list events are read-only; there is no activity model to load a single event from. */
  public function loadEvent(int $id): array
  {
    return [];
  }

  /**
   * Validity of price lists as calendar events: start and end of validity of active and draft lists.
   *
   * @param string $dateStart
   * @param string $dateEnd
   * @param array $filter
   *
   * @return array
   *
   */
  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    /** @var Models\PriceList $mPriceList */
    $mPriceList = $this->getModel(Models\PriceList::class);
    $t = $mPriceList->table;

    $query = $mPriceList->record
      ->with('OWNER')
      ->where($t . '.status', '!=', Models\PriceList::STATUS_ARCHIVED)
      ->where(function ($q) use ($t, $dateStart, $dateEnd) {
        $q->whereBetween($t . '.valid_from', [$dateStart, $dateEnd])->orWhereBetween($t . '.valid_to', [$dateStart, $dateEnd]);
      })
    ;

    if (isset($filter['idUser']) && $filter['idUser'] > 0) {
      $query = $query->where($t . '.id_owner', $filter['idUser']);
    }
    if (isset($filter['fOwnership']) && $filter['fOwnership'] == 1) {
      $query = $query->where($t . '.id_owner', $this->authProvider()->getUserId());
    }

    $events = [];
    foreach ($query->get() as $priceList) {
      foreach ([['valid_from', $this->translate('starts')], ['valid_to', $this->translate('ends')]] as [$column, $label]) {
        if (empty($priceList->{$column})) continue;
        $date = date('Y-m-d', strtotime((string) $priceList->{$column}));
        if ($date < $dateStart || $date > $dateEnd) continue;

        $events[] = [
          'id' => (int) $priceList->id,
          'start' => $date,
          'end' => $date,
          'allDay' => true,
          'title' => $priceList->code . ' ' . $priceList->name . ' ' . $label,
          'source' => 'price-lists',
          'id_owner' => $priceList->id_owner,
          'owner' => $priceList->OWNER?->nick,
          'completed' => false,
          'url' => 'price-lists/' . $priceList->id,
        ];
      }
    }

    return $events;
  }

}
