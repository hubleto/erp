<?php

namespace Hubleto\App\Community\PriceList\Controllers\Boards;

use Hubleto\App\Community\PriceList\Services\Counter;

class MarginWarnings extends \Hubleto\Erp\Controller
{
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    /** @var Counter $counter */
    $counter = $this->getService(Counter::class);
    $warnings = $counter->marginWarnings();

    $this->viewParams['total'] = count($warnings);
    $this->viewParams['items'] = $warnings;

    $this->setView('@Hubleto:App:Custom:PriceList/Boards/MarginWarnings.twig');
  }
}
