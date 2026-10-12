<?php

namespace Hubleto\App\Community\PriceLists\Controllers\Boards;

use Hubleto\App\Community\PriceLists\Services\Counter;

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

    $this->setView('@Hubleto:App:Community:PriceLists/Boards/MarginWarnings.twig');
  }
}
