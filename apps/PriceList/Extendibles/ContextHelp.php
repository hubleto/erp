<?php

namespace Hubleto\App\Community\PriceLists\Extendibles;

class ContextHelp extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      'price-lists' => [
        'en' => 'en/apps/custom/price-lists',
      ],
    ];
  }
}
