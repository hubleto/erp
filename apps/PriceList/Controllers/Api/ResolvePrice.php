<?php

namespace Hubleto\App\Community\PriceList\Controllers\Api;

use Hubleto\App\Community\PriceList\Services\PriceResolver;

class ResolvePrice extends ResolverApiController
{
  public function renderJson(): array
  {
    /** @var PriceResolver $resolver */
    $resolver = $this->getService(PriceResolver::class);

    return [
      'status' => 'success',
      'result' => $resolver->resolve($this->readQuery()),
    ];
  }
}
