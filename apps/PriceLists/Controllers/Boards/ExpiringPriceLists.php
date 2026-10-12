<?php

namespace Hubleto\App\Community\PriceLists\Controllers\Boards;

use Hubleto\App\Community\PriceLists\Services\Counter;

class ExpiringPriceLists extends \Hubleto\Erp\Controller
{
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    /** @var Counter $counter */
    $counter = $this->getService(Counter::class);
    $expiring = $counter->expiringPriceLists();

    $this->viewParams['total'] = count($expiring);
    $this->viewParams['items'] = $expiring;

    $this->setView('@Hubleto:App:Community:PriceLists/Boards/ExpiringPriceLists.twig');
  }
}
