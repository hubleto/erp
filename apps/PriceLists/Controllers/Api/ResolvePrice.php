<?php

namespace Hubleto\App\Community\PriceLists\Controllers\Api;

use Hubleto\App\Community\PriceLists\Services\PriceResolver;
use Hubleto\App\Community\PriceLists\Services\ApiQueryParser;
use Hubleto\Erp\Core;

class ResolvePrice extends Core
{
  public function renderJson(): array
  {
    /** @var ApiQueryParser */
    $parser = $this->getService(ApiQueryParser::class);

    /** @var PriceResolver $resolver */
    $resolver = $this->getService(PriceResolver::class);

    return [
      'status' => 'success',
      'result' => $resolver->resolve($parser->readQuery()),
    ];
  }
}
