<?php

namespace Hubleto\App\Community\Deals\Extendibles;

class ProductTypes extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      10 => 'deal.identifier',
      11 => 'deal.price_excl_vat',
    ];
  }

}