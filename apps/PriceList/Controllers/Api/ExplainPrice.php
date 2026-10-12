<?php

namespace Hubleto\App\Community\PriceList\Controllers\Api;

use Hubleto\App\Community\PriceList\Services\PriceResolver;

class ExplainPrice extends ResolverApiController
{
  public function renderJson(): array
  {
    /** @var PriceResolver $resolver */
    $resolver = $this->getService(PriceResolver::class);
    $result = $resolver->resolve($this->readQuery());

    return [
      'status' => 'success',
      'result' => $result,
      'trace' => $result['trace'],
    ];
  }
}
