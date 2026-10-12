<?php

namespace Hubleto\App\Community\PriceLists\Services;

use Hubleto\App\Community\PriceLists\Models\History;
use Hubleto\App\Community\PriceLists\Models\PriceList;

/**
 * Counters used in the sidebar badge, alerts, dashboards and crons.
 */
class Counter extends \Hubleto\Erp\Core
{
  /** Price lists waiting for activation. */
  public function draftPriceLists(): int
  {
    /** @var PriceList $mPriceList */
    $mPriceList = $this->getModel(PriceList::class);
    return (int) $mPriceList->record->where($mPriceList->table . '.status', PriceList::STATUS_DRAFT)->count();
  }

  /**
   * Active price lists that end within the given number of days.
   *
   * @param int|null $days Null = value of the setting expiryWarningDays
   *
   * @return array Price list records (as arrays)
   *
   */
  public function expiringPriceLists(?int $days = null): array
  {
    /** @var Settings $settings */
    $settings = $this->getService(Settings::class);
    $days = $days ?? $settings->getInt('expiryWarningDays');

    /** @var PriceList $mPriceList */
    $mPriceList = $this->getModel(PriceList::class);
    $t = $mPriceList->table;

    return $mPriceList->record
      ->where($t . '.status', PriceList::STATUS_ACTIVE)
      ->whereNotNull($t . '.valid_to')
      ->where($t . '.valid_to', '>=', date('Y-m-d'))
      ->where($t . '.valid_to', '<=', date('Y-m-d', strtotime('+' . $days . ' days')))
      ->orderBy($t . '.valid_to', 'asc')
      ->get()
      ->toArray()
    ;
  }

  /** Margin floor hits logged in the last N days. */
  public function marginWarnings(int $days = 30): array
  {
    /** @var History $mHistory */
    $mHistory = $this->getModel(History::class);
    $t = $mHistory->table;

    return $mHistory->record
      ->where($t . '.event_type', History::EVENT_MARGIN_FLOOR_HIT)
      ->where($t . '.date_time', '>=', date('Y-m-d H:i:s', strtotime('-' . $days . ' days')))
      ->orderBy($t . '.date_time', 'desc')
      ->get()
      ->toArray()
    ;
  }
}
