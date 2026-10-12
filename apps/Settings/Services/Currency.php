<?php

namespace Hubleto\App\Community\Settings\Services;

use Hubleto\Erp\Core;

class Currency extends Core
{

  public function getDefaultCurrencyId(): int
  {
    return $this->config()->getAsInteger('defaultCurrencyId');
  }

}