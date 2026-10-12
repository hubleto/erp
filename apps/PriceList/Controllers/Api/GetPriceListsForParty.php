<?php

namespace Hubleto\App\Community\PriceList\Controllers\Api;

use Hubleto\App\Community\PriceList\Services\PriceResolver;

class GetPriceListsForParty extends ResolverApiController
{
  public function renderJson(): array
  {
    /** @var PriceResolver $resolver */
    $resolver = $this->getService(PriceResolver::class);

    return [
      'status' => 'success',
      'priceLists' => $resolver->getApplicableLists($this->readQuery()),
    ];
  }
}
