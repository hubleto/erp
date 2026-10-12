<?php

namespace Hubleto\App\Community\PriceLists\Crons;

use Hubleto\App\Community\Notifications\Sender;
use Hubleto\App\Community\PriceLists\Models\PriceList;
use Hubleto\App\Community\PriceLists\Services\Counter;

/**
 * Notifies owners of price lists that are about to expire (within the setting expiryWarningDays).
 * Notifications are sent with Notifications\Sender.
 */
class WarnAboutExpiry extends \Hubleto\Erp\Cron
{
  public string $schedulingPattern = '0 7 * * *';

  public function run(): void
  {
    /** @var Counter $counter */
    $counter = $this->getService(Counter::class);
    /** @var Sender $sender */
    $sender = $this->getService(Sender::class);

    foreach ($counter->expiringPriceLists() as $priceList) {
      $idOwner = (int) ($priceList['id_owner'] ?? 0);
      if ($idOwner <= 0) continue;

      $sender->send(
        900,
        ['price-lists', 'expiry'],
        PriceList::class,
        (int) $priceList['id'],
        $idOwner,
        $this->translate('Price list is about to expire'),
        $priceList['code'] . ' ' . $priceList['name'] . ' - ' . $this->translate('valid to') . ' ' . $priceList['valid_to'],
        'price-lists/' . $priceList['id']
      );
    }
  }
}
