<?php

namespace Hubleto\App\Community\PriceList\Services;

use Hubleto\App\Community\PriceList\Models\History;

/**
 * Writes records to the history of a price list. A failure to write history never blocks the calling operation.
 */
class HistoryWriter extends \Hubleto\Erp\Core
{
  /**
   * @param int $idPriceList
   * @param int $eventType One of History::EVENT_*
   * @param string $description
   * @param array $extra Optional columns: id_item, id_product, old_value, new_value
   * @param string $source manual, bulk, sync, listener, cron
   *
   * @return void
   *
   */
  public function write(int $idPriceList, int $eventType, string $description, array $extra = [], string $source = 'manual'): void
  {
    if ($idPriceList <= 0) return;

    try {
      /** @var History $mHistory */
      $mHistory = $this->getModel(History::class);
      $mHistory->record->recordCreate(array_merge([
        'id_price_list' => $idPriceList,
        'event_type' => $eventType,
        'short_description' => $description,
        'date_time' => date('Y-m-d H:i:s'),
        'id_user' => $this->authProvider()->getUserId(),
        'source' => $source,
      ], $extra));
    } catch (\Throwable $e) {
      // history must never block saving of the price list
    }
  }
}
