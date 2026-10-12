<?php

namespace Hubleto\App\Community\PriceLists\Extendibles;

/**
 * Declares apps that read the config key `priceCalculatorService` (a class implementing
 * Hubleto\Erp\Interfaces\PriceCalculatorInterface). The Settings page of PriceList offers a switch for each of them.
 * Other apps can provide their own Extendibles\PriceCalculatorConsumers class (see community-apps-patches.md).
 */
class PriceCalculatorConsumers extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      \Hubleto\App\Community\Orders\Loader::class => [
        'title' => 'Orders',
        'configPath' => 'apps/Hubleto\\App\\Community\\Orders/priceCalculatorService',
      ],
      \Hubleto\App\Community\Deals\Loader::class => [
        'title' => 'Deals',
        'configPath' => 'apps/Hubleto\\App\\Community\\Deals/priceCalculatorService',
      ],
      \Hubleto\App\Community\Invoices\Loader::class => [
        'title' => 'Invoices',
        'configPath' => 'apps/Hubleto\\App\\Community\\Invoices/priceCalculatorService',
      ],
    ];
  }
}
