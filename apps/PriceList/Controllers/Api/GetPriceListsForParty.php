<?php

namespace Hubleto\App\Community\PriceList\Controllers\Api;

use Hubleto\App\Community\PriceList\Services\PriceResolver;
use Hubleto\App\Community\PriceList\Services\ApiQueryParser;
use Hubleto\Erp\Core;

class GetPriceListsForParty extends Core
{
  public function renderJson(): array
  {
    /** @var ApiQueryParser */
    $parser = $this->getService(ApiQueryParser::class);

    /** @var PriceResolver $resolver */
    $resolver = $this->getService(PriceResolver::class);

    return [
      'status' => 'success',
      'priceLists' => $resolver->getApplicableLists($parser->readQuery()),
    ];
  }
}
