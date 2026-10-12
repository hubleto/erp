<?php

namespace Hubleto\App\Community\PriceLists\Crons;

use Hubleto\App\Community\PriceLists\Models\History;
use Hubleto\App\Community\PriceLists\Models\Item;
use Hubleto\App\Community\PriceLists\Models\PriceList;
use Hubleto\App\Community\PriceLists\Models\Resolution;
use Hubleto\App\Community\PriceLists\Models\Rule;
use Hubleto\App\Community\PriceLists\Services\HistoryWriter;
use Hubleto\App\Community\PriceLists\Services\Settings;

/**
 * Archives price lists whose validity has ended, deactivates prices and rules that are no longer valid
 * and deletes old resolution records.
 */
class ActivateAndExpire extends \Hubleto\Erp\Cron
{
  public string $schedulingPattern = '5 0 * * *';

  public function run(): void
  {
    $today = date('Y-m-d');

    /** @var Settings $settings */
    $settings = $this->getService(Settings::class);
    /** @var HistoryWriter $historyWriter */
    $historyWriter = $this->getService(HistoryWriter::class);

    /** @var PriceList $mPriceList */
    $mPriceList = $this->getModel(PriceList::class);
    $expiredLists = $mPriceList->record
      ->where($mPriceList->table . '.status', PriceList::STATUS_ACTIVE)
      ->whereNotNull($mPriceList->table . '.valid_to')
      ->where($mPriceList->table . '.valid_to', '<', $today)
      ->get()
    ;
    foreach ($expiredLists as $priceList) {
      $mPriceList->record->where($mPriceList->table . '.id', (int) $priceList->id)->update(['status' => PriceList::STATUS_ARCHIVED, 'is_default' => 0]);
      $historyWriter->write((int) $priceList->id, History::EVENT_ARCHIVED, $this->translate('Price list archived because its validity ended'), [], 'cron');
    }

    /** @var Item $mItem */
    $mItem = $this->getModel(Item::class);
    $mItem->record
      ->where($mItem->table . '.is_active', 1)
      ->whereNotNull($mItem->table . '.valid_to')
      ->where($mItem->table . '.valid_to', '<', $today)
      ->update(['is_active' => 0])
    ;

    /** @var Rule $mRule */
    $mRule = $this->getModel(Rule::class);
    $mRule->record
      ->where($mRule->table . '.is_active', 1)
      ->whereNotNull($mRule->table . '.valid_to')
      ->where($mRule->table . '.valid_to', '<', $today)
      ->update(['is_active' => 0])
    ;

    /** @var Resolution $mResolution */
    $mResolution = $this->getModel(Resolution::class);
    $retentionDays = $settings->getInt('resolutionRetentionDays');
    if ($retentionDays > 0) {
      $mResolution->record
        ->where($mResolution->table . '.date_time', '<', date('Y-m-d H:i:s', strtotime('-' . $retentionDays . ' days')))
        ->delete()
      ;
    }
  }
}
