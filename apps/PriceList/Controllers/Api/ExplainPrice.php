<?php

namespace Hubleto\App\Community\PriceList\Controllers\Api;

use Hubleto\App\Community\PriceList\Services\PriceResolver;
use Hubleto\App\Community\PriceList\Services\ApiQueryParser;
use Hubleto\Erp\Core;

class ExplainPrice extends Core
{
  public function renderJson(): array
  {
    /** @var ApiQueryParser */
    $parser = $this->getService(ApiQueryParser::class);

    /** @var PriceResolver */
    $resolver = $this->getService(PriceResolver::class);

    $result = $resolver->resolve($parser->readQuery());

    return [
      'status' => 'success',
      'result' => $result,
      'trace' => $result['trace'],
    ];
  }
}
